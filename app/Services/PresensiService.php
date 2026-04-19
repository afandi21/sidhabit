<?php

namespace App\Services;

use App\Models\Presensi;
use App\Models\JadwalMengajar;
use App\Models\Dosen;
use App\Models\LokasiKampus;
use App\Models\HariLibur;
use App\Models\Pengaturan;
use Carbon\Carbon;

class PresensiService
{
    /**
     * Get jadwal for a dosen today
     */
    public function getJadwalHariIni(int $dosenId): \Illuminate\Database\Eloquent\Collection
    {
        $namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $hariIni = $namaHari[now()->dayOfWeek];

        return JadwalMengajar::where('dosen_id', $dosenId)
            ->where('is_active', true)
            ->whereHas('semester', fn($q) => $q->where('is_active', true))
            ->whereHas('hari', fn($q) => $q->where('nama_hari', $hariIni))
            ->with(['mataKuliah', 'ruangan', 'hari'])
            ->orderBy('jam_mulai')
            ->get();
    }

    /**
     * Validate GPS location is within any active campus
     */
    public function validateLocation(float $lat, float $lng): array
    {
        $lokasiList = LokasiKampus::active()->get();

        foreach ($lokasiList as $lokasi) {
            if ($lokasi->isWithinRadius($lat, $lng)) {
                return [
                    'valid' => true,
                    'lokasi' => $lokasi->nama_lokasi,
                    'jarak' => round($lokasi->calculateDistance($lat, $lng)),
                ];
            }
        }

        $nearestDistance = $lokasiList->map(fn($l) => $l->calculateDistance($lat, $lng))->min();

        return [
            'valid' => false,
            'lokasi' => null,
            'jarak' => round($nearestDistance ?? 0),
            'message' => 'Anda berada di luar area kampus.',
        ];
    }

    /**
     * Process clock-in
     */
    public function clockIn(Dosen $dosen, int $jadwalId, float $lat, float $lng, ?string $deviceFingerprint = null): array
    {
        // Check hari libur
        if (HariLibur::isLibur(today())) {
            return ['success' => false, 'message' => 'Hari ini adalah hari libur.'];
        }

        // Validate location
        $locationCheck = $this->validateLocation($lat, $lng);
        if (!$locationCheck['valid']) {
            return ['success' => false, 'message' => $locationCheck['message']];
        }

        // Check jadwal
        $jadwal = JadwalMengajar::where('id', $jadwalId)
            ->where('dosen_id', $dosen->id)
            ->where('is_active', true)
            ->first();

        if (!$jadwal) {
            return ['success' => false, 'message' => 'Jadwal tidak ditemukan.'];
        }

        // Check if already clocked in today for this jadwal
        $existing = Presensi::where('dosen_id', $dosen->id)
            ->where('jadwal_mengajar_id', $jadwalId)
            ->where('tanggal', today())
            ->first();

        if ($existing && $existing->jam_masuk) {
            return ['success' => false, 'message' => 'Anda sudah melakukan presensi masuk untuk jadwal ini.'];
        }

        // Determine status (hadir or terlambat)
        $jamMasuk = now()->format('H:i:s');
        $toleransi = (int) Pengaturan::getValue('toleransi_terlambat', 15);
        $batasWaktu = Carbon::parse($jadwal->jam_mulai)->addMinutes($toleransi);
        $status = now()->gt($batasWaktu) ? 'terlambat' : 'hadir';

        // Count pertemuan_ke
        $pertemuanKe = Presensi::where('dosen_id', $dosen->id)
            ->where('jadwal_mengajar_id', $jadwalId)
            ->count() + 1;

        // Create presensi
        $presensi = Presensi::create([
            'dosen_id' => $dosen->id,
            'jadwal_mengajar_id' => $jadwalId,
            'tanggal' => today(),
            'pertemuan_ke' => $pertemuanKe,
            'jam_masuk' => $jamMasuk,
            'status' => $status,
            'metode_presensi' => 'fingerprint',
            'latitude_masuk' => $lat,
            'longitude_masuk' => $lng,
            'device_fingerprint' => $deviceFingerprint,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return [
            'success' => true,
            'message' => 'Presensi masuk berhasil dicatat.',
            'data' => [
                'status' => $status,
                'jam_masuk' => $jamMasuk,
                'pertemuan_ke' => $pertemuanKe,
                'lokasi' => $locationCheck['lokasi'],
            ],
        ];
    }

    /**
     * Process clock-out
     */
    public function clockOut(Dosen $dosen, int $jadwalId, float $lat, float $lng): array
    {
        $presensi = Presensi::where('dosen_id', $dosen->id)
            ->where('jadwal_mengajar_id', $jadwalId)
            ->where('tanggal', today())
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->first();

        if (!$presensi) {
            return ['success' => false, 'message' => 'Tidak ditemukan presensi masuk untuk jadwal ini.'];
        }

        $jamKeluar = now()->format('H:i:s');
        $masuk = Carbon::parse($presensi->jam_masuk);
        $keluar = Carbon::parse($jamKeluar);
        $durasi = $keluar->diffInMinutes($masuk);

        $presensi->update([
            'jam_keluar' => $jamKeluar,
            'durasi_menit' => $durasi,
            'latitude_keluar' => $lat,
            'longitude_keluar' => $lng,
        ]);

        return [
            'success' => true,
            'message' => 'Presensi keluar berhasil dicatat.',
            'data' => [
                'jam_keluar' => $jamKeluar,
                'durasi_menit' => $durasi,
            ],
        ];
    }

    /**
     * Get dashboard statistics for today
     */
    public function getStatistikHariIni(): array
    {
        $today = today();

        $totalDosen = Dosen::aktif()->count();
        $hadir = Presensi::where('tanggal', $today)->whereIn('status', ['hadir', 'terlambat'])->distinct('dosen_id')->count('dosen_id');
        $terlambat = Presensi::where('tanggal', $today)->where('status', 'terlambat')->distinct('dosen_id')->count('dosen_id');
        $izin = Presensi::where('tanggal', $today)->whereIn('status', ['izin', 'sakit', 'cuti'])->distinct('dosen_id')->count('dosen_id');
        $alfa = $totalDosen - $hadir - $izin;

        return [
            'total_dosen' => $totalDosen,
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'izin' => $izin,
            'alfa' => max(0, $alfa),
            'persentase' => $totalDosen > 0 ? round(($hadir / $totalDosen) * 100, 1) : 0,
        ];
    }
}
