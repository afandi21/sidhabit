<?php

namespace App\Services;

use App\Models\JadwalMengajar;
use App\Models\BebanMengajar;
use App\Models\Semester;
use App\Models\Hari;
use App\Models\Ruangan;
use App\Models\SesiKuliah;
use App\Models\DosenKetersediaan;

class JadwalService
{
    /**
     * Memeriksa apakah ada jadwal yang bentrok (Dosen atau Ruangan pada waktu yang sama)
     */
    public function checkConflict($semesterId, $hariId, $dosenId, $ruanganId, $jamMulai, $jamSelesai, $excludeJadwalId = null): bool
    {
        $query = JadwalMengajar::where('semester_id', $semesterId)
            ->where('hari_id', $hariId)
            ->where(function ($q) use ($dosenId, $ruanganId) {
                $q->where('dosen_id', $dosenId)
                  ->orWhere('ruangan_id', $ruanganId);
            })
            ->where('jam_mulai', '<', $jamSelesai)
            ->where('jam_selesai', '>', $jamMulai);

        if ($excludeJadwalId) {
            $query->where('id', '!=', $excludeJadwalId);
        }

        return $query->exists();
    }

    /**
     * Mengembalikan SKS ke beban mengajar sebelum jadwal dihapus
     */
    public function returnSksQuota(JadwalMengajar $jadwal): void
    {
        $beban = BebanMengajar::where('dosen_id', $jadwal->dosen_id)
            ->where('mata_kuliah_id', $jadwal->mata_kuliah_id)
            ->where('semester_id', $jadwal->semester_id)
            ->where('kelas', $jadwal->kelas)
            ->first();

        if ($beban) {
            $sks_dikembalikan = SesiKuliah::where('id', '>=', $jadwal->sesi_mulai_id)
                ->where('id', '<=', $jadwal->sesi_selesai_id)
                ->count() ?: 1;
            
            if ($sks_dikembalikan > 0) {
                $beban->sks_terjadwal = max(0, $beban->sks_terjadwal - $sks_dikembalikan);
                $beban->save();
            }
        }
    }

    /**
     * Melakukan proses auto-generate jadwal mengajar
     */
    public function autoGenerate(Semester $semesterAktif): int
    {
        $bebans = BebanMengajar::where('semester_id', $semesterAktif->id)
            ->whereColumn('sks_terjadwal', '<', 'total_sks')
            ->get();

        if ($bebans->isEmpty()) {
            return 0; // Tidak ada yang digenerate
        }

        $haris = Hari::orderBy('urutan')->get();
        $ruangans = Ruangan::active()->get();
        $sesis = SesiKuliah::active()->get();
        $sesi_count = $sesis->count();

        $generatedCount = 0;

        foreach ($bebans as $beban) {
            $sks_remaining = $beban->total_sks - $beban->sks_terjadwal;
            
            $dosenAvailableDays = DosenKetersediaan::where('dosen_id', $beban->dosen_id)
                ->where('is_bersedia', true)
                ->pluck('hari_id')->toArray();

            if (empty($dosenAvailableDays)) {
                $dosenAvailableDays = $haris->pluck('id')->toArray();
            }

            while ($sks_remaining > 0) {
                $placed_sks = 0;

                for ($len = $sks_remaining; $len >= 1; $len--) {
                    foreach ($dosenAvailableDays as $h_id) {
                        $hari = $haris->firstWhere('id', $h_id);
                        if (!$hari) continue;

                        $max_sesi = ($hari->nama_hari == 'Kamis') ? 4 : min(8, $sesi_count);

                        foreach ($ruangans as $ruangan) {
                            for ($start_idx = 0; $start_idx <= $max_sesi - $len; $start_idx++) {
                                $sesi_window = $sesis->slice($start_idx, $len);
                                if ($sesi_window->count() < $len) continue;

                                $window_mulai = $sesi_window->first()->jam_mulai;
                                $window_selesai = $sesi_window->last()->jam_selesai;

                                $conflict = $this->checkConflict(
                                    $semesterAktif->id, $h_id, $beban->dosen_id, $ruangan->id, 
                                    $window_mulai, $window_selesai
                                );

                                if (!$conflict) {
                                    JadwalMengajar::create([
                                        'dosen_id' => $beban->dosen_id,
                                        'mata_kuliah_id' => $beban->mata_kuliah_id,
                                        'ruangan_id' => $ruangan->id,
                                        'semester_id' => $semesterAktif->id,
                                        'hari_id' => $h_id,
                                        'sesi_mulai_id' => $sesi_window->first()->id,
                                        'sesi_selesai_id' => $sesi_window->last()->id,
                                        'kelas' => $beban->kelas,
                                        'jam_mulai' => $window_mulai,
                                        'jam_selesai' => $window_selesai,
                                        'jumlah_pertemuan' => 16,
                                        'is_active' => true,
                                    ]);

                                    $placed_sks = $len;
                                    $beban->sks_terjadwal += $len;
                                    $beban->save();
                                    
                                    $generatedCount++;
                                    break 4; 
                                }
                            }
                        }
                    }
                }

                if ($placed_sks == 0) break;
                $sks_remaining -= $placed_sks;
            }
        }

        return $generatedCount;
    }
}
