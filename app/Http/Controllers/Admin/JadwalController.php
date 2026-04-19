<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalMengajar;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\Ruangan;
use App\Models\Semester;
use App\Models\Hari;
use App\Models\SesiKuliah;
use App\Models\BebanMengajar;
use App\Models\DosenKetersediaan;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        $query = JadwalMengajar::with(['dosen', 'mataKuliah.programStudi', 'ruangan', 'hari', 'semester'])
            ->whereHas('semester', fn($q) => $q->where('is_active', true));

        // Filter Operator Prodi (Hanya lihat jadwal prodi-nya saja)
        if (auth()->user()->isOperatorProdi()) {
            $query->whereHas('mataKuliah', function ($q) {
                $q->where('program_studi_id', auth()->user()->program_studi_id);
            });
        }

        $jadwals = $query->orderBy('hari_id')->orderBy('jam_mulai')->get();
            
        return view('admin.jadwal.index', compact('jadwals'));
    }

    public function create()
    {
        $activeSemester = Semester::where('is_active', true)->first();
        $dosens = Dosen::aktif()->get();
        
        $mkQuery = MataKuliah::where('is_active', true);
        if ($activeSemester) {
            if (str_contains(strtolower($activeSemester->nama_semester), 'ganjil')) {
                $mkQuery->whereRaw('semester % 2 != 0');
            } else if (str_contains(strtolower($activeSemester->nama_semester), 'genap')) {
                $mkQuery->whereRaw('semester % 2 = 0');
            }
        }
        $matkuls = $mkQuery->get();
        
        $ruangans = Ruangan::active()->get();
        $semesters = Semester::active()->get();
        $haris = Hari::orderBy('urutan')->get();
        $sesis = SesiKuliah::active()->get();

        return view('admin.jadwal.create', compact('dosens', 'matkuls', 'ruangans', 'semesters', 'haris', 'sesis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dosen_id' => 'required|exists:dosens,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'ruangan_id' => 'required|exists:ruangan,id',
            'semester_id' => 'required|exists:semesters,id',
            'hari_id' => 'required|exists:hari,id',
            'sesi_mulai_id' => 'required|exists:sesi_kuliah,id',
            'sesi_selesai_id' => 'required|exists:sesi_kuliah,id',
            'kelas' => 'nullable|string|max:10',
        ]);

        $sesiMulai = SesiKuliah::find($request->sesi_mulai_id);
        $sesiSelesai = SesiKuliah::find($request->sesi_selesai_id);

        if ($sesiSelesai->jam_selesai <= $sesiMulai->jam_mulai) {
            return back()->with('error', 'Sesi selesai harus setelah sesi mulai.')->withInput();
        }

        $data = $request->all();
        $data['jam_mulai'] = $sesiMulai->jam_mulai;
        $data['jam_selesai'] = $sesiSelesai->jam_selesai;

        // Validasi bentrok dosen & ruangan
        $bentrok = JadwalMengajar::where('semester_id', $request->semester_id)
            ->where('hari_id', $request->hari_id)
            ->where(function($query) use ($request) {
                $query->where('dosen_id', $request->dosen_id)
                      ->orWhere('ruangan_id', $request->ruangan_id);
            })
            ->where('jam_mulai', '<', $data['jam_selesai'])
            ->where('jam_selesai', '>', $data['jam_mulai'])
            ->exists();

        if ($bentrok) {
            return back()->with('error', 'Gagal! Dosen bersangkutan atau Ruangan sudah memiliki jadwal aktif yang beririsan (bentrok) pada waktu tersebut.')->withInput();
        }

        JadwalMengajar::create($data);

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal kuliah berhasil ditambahkan.');
    }

    public function edit(JadwalMengajar $jadwal)
    {
        $dosens = Dosen::aktif()->get();
        $matkuls = MataKuliah::where('is_active', true)->get();
        $ruangans = Ruangan::active()->get();
        $semesters = Semester::all();
        $haris = Hari::orderBy('urutan')->get();
        $sesis = SesiKuliah::active()->get();

        return view('admin.jadwal.edit', compact('jadwal', 'dosens', 'matkuls', 'ruangans', 'semesters', 'haris', 'sesis'));
    }

    public function update(Request $request, JadwalMengajar $jadwal)
    {
        $request->validate([
            'dosen_id' => 'required|exists:dosens,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'ruangan_id' => 'required|exists:ruangan,id',
            'semester_id' => 'required|exists:semesters,id',
            'hari_id' => 'required|exists:hari,id',
            'sesi_mulai_id' => 'required|exists:sesi_kuliah,id',
            'sesi_selesai_id' => 'required|exists:sesi_kuliah,id',
        ]);

        $sesiMulai = SesiKuliah::find($request->sesi_mulai_id);
        $sesiSelesai = SesiKuliah::find($request->sesi_selesai_id);

        $data = $request->all();
        $data['jam_mulai'] = $sesiMulai->jam_mulai;
        $data['jam_selesai'] = $sesiSelesai->jam_selesai;

        // Validasi bentrok saat update dosen & ruangan
        $bentrokUpdate = JadwalMengajar::where('semester_id', $request->semester_id)
            ->where('hari_id', $request->hari_id)
            ->where('id', '!=', $jadwal->id) // Jangan memvalidasi bentrok dengan dirinya sendiri
            ->where(function($query) use ($request) {
                $query->where('dosen_id', $request->dosen_id)
                      ->orWhere('ruangan_id', $request->ruangan_id);
            })
            ->where('jam_mulai', '<', $data['jam_selesai'])
            ->where('jam_selesai', '>', $data['jam_mulai'])
            ->exists();

        if ($bentrokUpdate) {
            return back()->with('error', 'Gagal! Dosen bersangkutan atau Ruangan sudah terpakai pada waktu tersebut (Bentrok).')->withInput();
        }

        $jadwal->update($data);

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal kuliah berhasil diperbarui.');
    }

    public function autoGenerate()
    {
        // Ambil semester yang sedang aktif
        $semesterAktif = Semester::where('is_active', true)->first();
        if (!$semesterAktif) {
            return back()->with('error', 'Tidak ada semester aktif untuk di-generate.');
        }

        $bebans = BebanMengajar::where('semester_id', $semesterAktif->id)
            ->whereColumn('sks_terjadwal', '<', 'total_sks')
            ->get();

        if ($bebans->isEmpty()) {
            return back()->with('error', 'Semua beban mengajar sudah terjadwal atau belum ada beban untuk semester ini.');
        }

        $haris = Hari::orderBy('urutan')->get();
        $ruangans = Ruangan::active()->get();
        $sesis = SesiKuliah::active()->get();
        $sesi_count = $sesis->count();

        $generatedCount = 0;

        foreach ($bebans as $beban) {
            $sks_remaining = $beban->total_sks - $beban->sks_terjadwal;
            
            // Hari-hari dimana dosen tsb BISA mengajar
            $dosenAvailableDays = DosenKetersediaan::where('dosen_id', $beban->dosen_id)
                ->where('is_bersedia', true)
                ->pluck('hari_id')->toArray();

            // Jika dosen tidak ada setup ketersediaan sama sekali, asumsikan bisa setiap hari
            if (empty($dosenAvailableDays)) {
                $dosenAvailableDays = $haris->pluck('id')->toArray();
            }

            while ($sks_remaining > 0) {
                $placed_sks = 0;

                // Coba blok berurutan mulai dari panjang max remaining SKS s/d 1 SKS
                for ($len = $sks_remaining; $len >= 1; $len--) {
                    foreach ($dosenAvailableDays as $h_id) {
                        $hari = $haris->firstWhere('id', $h_id);
                        if (!$hari) continue;

                        $max_sesi = ($hari->nama_hari == 'Kamis') ? 4 : min(8, $sesi_count);

                        foreach ($ruangans as $ruangan) {
                            // Cek di setiap kemungkinan mulainya blok sesi
                            for ($start_idx = 0; $start_idx <= $max_sesi - $len; $start_idx++) {
                                // Potong koleksi sesi seukuran blok (contiguous windows)
                                $sesi_window = $sesis->slice($start_idx, $len);
                                
                                if ($sesi_window->count() < $len) continue; // Pengecekan safety

                                $window_mulai = $sesi_window->first()->jam_mulai;
                                $window_selesai = $sesi_window->last()->jam_selesai;

                                // Cari konflik
                                $conflict = JadwalMengajar::where('semester_id', $semesterAktif->id)
                                    ->where('hari_id', $h_id)
                                    ->where(function ($q) use ($beban, $ruangan) {
                                        $q->where('dosen_id', $beban->dosen_id)
                                          ->orWhere('ruangan_id', $ruangan->id);
                                    })
                                    ->where('jam_mulai', '<', $window_selesai)
                                    ->where('jam_selesai', '>', $window_mulai)
                                    ->exists();

                                if (!$conflict) {
                                    // Ditemukan slot! 
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
                                    
                                    // Keluar dari semua loop pencarian, kembali ke loop `while` dengan sisa sks baru
                                    break 4; 
                                }
                            }
                        }
                    }
                }

                // Jika sudah coba semua kemungkinan panjang sesi, semua hari tersedia, dan semua ruangan TAPI tidak ketemu
                if ($placed_sks == 0) {
                    break; // Berhenti paksa agar tidak infinite loop
                }

                $sks_remaining -= $placed_sks;
            }
        }

        return redirect()->route('admin.jadwal.index')->with('success', "Proses Generate selesai! $generatedCount jadwal (pecahan sesi) berhasil dibuat secara otomatis.");
    }

    public function board()
    {
        $semesterAktif = Semester::where('is_active', true)->first();
        if (!$semesterAktif) {
            return redirect()->route('admin.jadwal.index')->with('error', 'Tidak ada semester aktif.');
        }

        // Draggable items (Beban yang belum selesai)
        $bebanQuery = BebanMengajar::with(['dosen', 'mataKuliah.programStudi'])
            ->where('semester_id', $semesterAktif->id)
            ->whereColumn('sks_terjadwal', '<', 'total_sks');

        if (auth()->user()->isOperatorProdi()) {
            $bebanQuery->whereHas('mataKuliah', function ($q) {
                $q->where('program_studi_id', auth()->user()->program_studi_id);
            });
        }
        $bebans = $bebanQuery->get();

        $haris = Hari::orderBy('urutan')->get();
        $ruangans = Ruangan::active()->get();
        $sesis = SesiKuliah::active()->get();

        // Ambil SEMUA jadwal untuk blokir cell (Karena konflik bisa lintas prodi)
        // Tetapi untuk tampilan text di cell, kita mungkin hanya ingin menampilkan detailnya jika itu prodi dia, 
        // atau tetap menampilkan nama MK-nya agar dia tahu ruangannya benar-benar terpakai.
        // Asumsi user behavior: Tampilkan semua agar transparan ruangan tersebut dipakai prodi apa.
        $rawJadwals = JadwalMengajar::with(['dosen', 'mataKuliah.programStudi'])
            ->where('semester_id', $semesterAktif->id)
            ->get();

        // Kita map jadwal berdasarkan key: {ruangan_id}_{hari_id}_{sesi_mulai_id}
        // Catatan: Jika 1 tabel jadwal memakan 2 sesi, di grid UI kita bisa menampilkan hanya di mulainya saja
        // atau kita beri keterangan durasi, untuk grid simple 1 cell = 1 sesi, kita bisa mem-map-kan ke setiap rentang
        // Agar drag-drop stabil (1 sks = 1 drag), grid berbasis 1 sesi sangat direkomendasikan.
        $jadwals = [];
        foreach ($rawJadwals as $j) {
            // Kita lock cell berdasarkan sesi mulainya. 
            // Untuk jadwal multi-sesi, lebih baik diletakkan jika model gridnya mendukung row-span, 
            // Namun untuk array mapping, mapping di Sesi Mulainya saja dulu.
            $key = $j->ruangan_id . '_' . $j->hari_id . '_' . $j->sesi_mulai_id;
            $jadwals[$key] = $j;
            
            // Tandai sesi turunannya jika blok
            if ($j->sesi_mulai_id != $j->sesi_selesai_id) {
                $start = $sesis->where('id', $j->sesi_mulai_id)->keys()->first();
                $end = $sesis->where('id', $j->sesi_selesai_id)->keys()->first();
                if ($start !== false && $end !== false && $end > $start) {
                    for ($k = $start + 1; $k <= $end; $k++) {
                        $sId = $sesis->values()[$k]->id;
                        $jadwals[$j->ruangan_id . '_' . $j->hari_id . '_' . $sId] = 'BLOCKED_BY_'.$j->id;
                    }
                }
            }
        }

        return view('admin.jadwal.board', compact('semesterAktif', 'bebans', 'haris', 'ruangans', 'sesis', 'jadwals'));
    }

    public function boardDrop(Request $request)
    {
        $request->validate([
            'beban_id' => 'required|exists:beban_mengajar,id',
            'hari_id' => 'required|exists:hari,id',
            'sesi_id' => 'required|exists:sesi_kuliah,id',
            'ruangan_id' => 'required|exists:ruangan,id',
        ]);

        $beban = BebanMengajar::findOrFail($request->beban_id);
        $sesi = SesiKuliah::findOrFail($request->sesi_id);

        if ($beban->sks_terjadwal >= $beban->total_sks) {
            return response()->json(['success' => false, 'message' => 'Mata kuliah ini sudah memenuhi kuota SKS.']);
        }

        // Validasi ketersediaan dosen
        $isBersedia = DosenKetersediaan::where('dosen_id', $beban->dosen_id)
            ->where('hari_id', $request->hari_id)
            ->value('is_bersedia');

        if ($isBersedia === 0) { // Secara eksplisit false
            return response()->json(['success' => false, 'message' => 'Dosen tidak bersedia mengajar di hari ini.']);
        }

        // Cek Bentrok Waktu (Dosen tidak bisa 2 tempat, Ruangan tidak bisa 2 penghuni)
        $conflict = JadwalMengajar::where('semester_id', $beban->semester_id)
            ->where('hari_id', $request->hari_id)
            ->where(function ($q) use ($beban, $request) {
                $q->where('dosen_id', $beban->dosen_id)
                  ->orWhere('ruangan_id', $request->ruangan_id);
            })
            ->where('jam_mulai', '<', $sesi->jam_selesai)
            ->where('jam_selesai', '>', $sesi->jam_mulai)
            ->exists();

        if ($conflict) {
            return response()->json(['success' => false, 'message' => 'Bentrok! Ruangan sudah terisi atau Dosen memiliki kelas lain di jam ini.']);
        }

        // Simpan
        $jadwal = JadwalMengajar::create([
            'dosen_id' => $beban->dosen_id,
            'mata_kuliah_id' => $beban->mata_kuliah_id,
            'ruangan_id' => $request->ruangan_id,
            'semester_id' => $beban->semester_id,
            'hari_id' => $request->hari_id,
            'sesi_mulai_id' => $sesi->id,
            'sesi_selesai_id' => $sesi->id, // Drag & drop membuat 1 Sesi per kotak, jika ingin memperbesar bisa digabung manual nanti
            'kelas' => $beban->kelas,
            'jam_mulai' => $sesi->jam_mulai,
            'jam_selesai' => $sesi->jam_selesai,
            'jumlah_pertemuan' => 16,
            'is_active' => true,
        ]);

        $beban->sks_terjadwal += 1;
        $beban->save();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil ditambahkan.',
            'sks_terjadwal' => $beban->sks_terjadwal,
            'total_sks' => $beban->total_sks,
            'html' => '<div class="badge bg-primary text-wrap text-start p-2 w-100 mb-1 shadow-sm"><div class="fw-bold fs-7">'.$beban->mataKuliah->nama_mk.'</div><small>'.$beban->dosen->nama_gelar.' ('.$beban->kelas.')</small></div>'
        ]);
    }

    public function destroy(JadwalMengajar $jadwal)
    {
        // Kembalikan sisa SKS ke Beban Mengajar sebelum dihapus
        $beban = BebanMengajar::where('dosen_id', $jadwal->dosen_id)
            ->where('mata_kuliah_id', $jadwal->mata_kuliah_id)
            ->where('semester_id', $jadwal->semester_id)
            ->where('kelas', $jadwal->kelas)
            ->first();

        if ($beban) {
            // Hitung durasi sesi (SKS) yang dipakai oleh jadwal ini dengan mengandalkan urutan ID Sesi
            $sks_dikembalikan = \App\Models\SesiKuliah::where('id', '>=', $jadwal->sesi_mulai_id)
                                          ->where('id', '<=', $jadwal->sesi_selesai_id)
                                          ->count() ?: 1;
            
            if ($sks_dikembalikan > 0) {
                // Pastikan tidak minus
                $beban->sks_terjadwal = max(0, $beban->sks_terjadwal - $sks_dikembalikan);
                $beban->save();
            }
        }

        $jadwal->delete();
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal kuliah berhasil dihapus dan kuota SKS telah dikembalikan.');
    }
}
