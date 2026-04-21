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
    protected $jadwalService;

    public function __construct(\App\Services\JadwalService $jadwalService)
    {
        $this->jadwalService = $jadwalService;
    }

    public function index()
    {
        $query = JadwalMengajar::with(['dosen', 'mataKuliah.programStudi', 'ruangan', 'hari', 'semester'])
            ->whereHas('semester', fn($q) => $q->where('is_active', true))
            ->byRole('program_studi_id', 'mataKuliah');

        $jadwals = $query->orderBy('hari_id')->orderBy('jam_mulai')->get();
        return view('admin.jadwal.index', compact('jadwals'));
    }

    public function create()
    {
        $activeSemester = Semester::where('is_active', true)->first();
        $dosens = Dosen::active()->get();
        
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
        $data = $request->validate([
            'dosen_id' => 'required|exists:dosens,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'ruangan_id' => 'required|exists:ruangan,id',
            'semester_id' => 'required|exists:semesters,id',
            'hari_id' => 'required|exists:hari,id',
            'sesi_mulai_id' => 'required|exists:sesi_kuliah,id',
            'sesi_selesai_id' => 'required|exists:sesi_kuliah,id',
            'kelas' => 'nullable|string|max:10',
        ]);

        $sesiMulai = SesiKuliah::find($data['sesi_mulai_id']);
        $sesiSelesai = SesiKuliah::find($data['sesi_selesai_id']);

        if ($sesiSelesai->jam_selesai <= $sesiMulai->jam_mulai) {
            return back()->with('error', 'Sesi selesai harus setelah sesi mulai.')->withInput();
        }

        $data['jam_mulai'] = $sesiMulai->jam_mulai;
        $data['jam_selesai'] = $sesiSelesai->jam_selesai;

        if ($this->jadwalService->checkConflict($data['semester_id'], $data['hari_id'], $data['dosen_id'], $data['ruangan_id'], $data['jam_mulai'], $data['jam_selesai'])) {
            return back()->with('error', 'Gagal! Dosen bersangkutan atau Ruangan sudah memiliki jadwal aktif yang beririsan (bentrok) pada waktu tersebut.')->withInput();
        }

        JadwalMengajar::create($data);
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal kuliah berhasil ditambahkan.');
    }

    public function edit(JadwalMengajar $jadwal)
    {
        $dosens = Dosen::active()->get();
        $matkuls = MataKuliah::where('is_active', true)->get();
        $ruangans = Ruangan::active()->get();
        $semesters = Semester::all();
        $haris = Hari::orderBy('urutan')->get();
        $sesis = SesiKuliah::active()->get();

        return view('admin.jadwal.edit', compact('jadwal', 'dosens', 'matkuls', 'ruangans', 'semesters', 'haris', 'sesis'));
    }

    public function update(Request $request, JadwalMengajar $jadwal)
    {
        $data = $request->validate([
            'dosen_id' => 'required|exists:dosens,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'ruangan_id' => 'required|exists:ruangan,id',
            'semester_id' => 'required|exists:semesters,id',
            'hari_id' => 'required|exists:hari,id',
            'sesi_mulai_id' => 'required|exists:sesi_kuliah,id',
            'sesi_selesai_id' => 'required|exists:sesi_kuliah,id',
        ]);

        $sesiMulai = SesiKuliah::find($data['sesi_mulai_id']);
        $sesiSelesai = SesiKuliah::find($data['sesi_selesai_id']);

        $data['jam_mulai'] = $sesiMulai->jam_mulai;
        $data['jam_selesai'] = $sesiSelesai->jam_selesai;

        if ($this->jadwalService->checkConflict($data['semester_id'], $data['hari_id'], $data['dosen_id'], $data['ruangan_id'], $data['jam_mulai'], $data['jam_selesai'], $jadwal->id)) {
            return back()->with('error', 'Gagal! Dosen bersangkutan atau Ruangan sudah terpakai pada waktu tersebut (Bentrok).')->withInput();
        }

        $jadwal->update($data);
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal kuliah berhasil diperbarui.');
    }

    public function autoGenerate()
    {
        $semesterAktif = Semester::where('is_active', true)->first();
        if (!$semesterAktif) {
            return back()->with('error', 'Tidak ada semester aktif untuk di-generate.');
        }

        $generatedCount = $this->jadwalService->autoGenerate($semesterAktif);

        if ($generatedCount === 0) {
            return back()->with('error', 'Semua beban mengajar sudah terjadwal atau belum ada beban untuk semester ini.');
        }

        return redirect()->route('admin.jadwal.index')->with('success', "Proses Generate selesai! $generatedCount jadwal (pecahan sesi) berhasil dibuat secara otomatis.");
    }

    public function board()
    {
        $semesterAktif = Semester::where('is_active', true)->first();
        if (!$semesterAktif) {
            return redirect()->route('admin.jadwal.index')->with('error', 'Tidak ada semester aktif.');
        }

        $bebanQuery = BebanMengajar::with(['dosen', 'mataKuliah.programStudi'])
            ->where('semester_id', $semesterAktif->id)
            ->whereColumn('sks_terjadwal', '<', 'total_sks');

        if (auth()->user()->isOperatorProdi()) {
            $bebanQuery->whereHas('mataKuliah', fn($q) => $q->where('program_studi_id', auth()->user()->program_studi_id));
        }
        
        $bebans = $bebanQuery->get();
        $haris = Hari::orderBy('urutan')->get();
        $ruangans = Ruangan::active()->get();
        $sesis = SesiKuliah::active()->get();

        $rawJadwals = JadwalMengajar::with(['dosen', 'mataKuliah.programStudi'])
            ->where('semester_id', $semesterAktif->id)
            ->get();

        $jadwals = [];
        foreach ($rawJadwals as $j) {
            $jadwals[$j->ruangan_id . '_' . $j->hari_id . '_' . $j->sesi_mulai_id] = $j;
            
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
        $data = $request->validate([
            'beban_id' => 'required|exists:beban_mengajar,id',
            'hari_id' => 'required|exists:hari,id',
            'sesi_id' => 'required|exists:sesi_kuliah,id',
            'ruangan_id' => 'required|exists:ruangan,id',
        ]);

        $beban = BebanMengajar::findOrFail($data['beban_id']);
        $sesi = SesiKuliah::findOrFail($data['sesi_id']);

        if ($beban->sks_terjadwal >= $beban->total_sks) {
            return response()->json(['success' => false, 'message' => 'Mata kuliah ini sudah memenuhi kuota SKS.']);
        }

        $isBersedia = DosenKetersediaan::where('dosen_id', $beban->dosen_id)
            ->where('hari_id', $data['hari_id'])
            ->value('is_bersedia');

        if ($isBersedia === 0) {
            return response()->json(['success' => false, 'message' => 'Dosen tidak bersedia mengajar di hari ini.']);
        }

        if ($this->jadwalService->checkConflict($beban->semester_id, $data['hari_id'], $beban->dosen_id, $data['ruangan_id'], $sesi->jam_mulai, $sesi->jam_selesai)) {
            return response()->json(['success' => false, 'message' => 'Bentrok! Ruangan sudah terisi atau Dosen memiliki kelas lain di jam ini.']);
        }

        JadwalMengajar::create([
            'dosen_id' => $beban->dosen_id,
            'mata_kuliah_id' => $beban->mata_kuliah_id,
            'ruangan_id' => $data['ruangan_id'],
            'semester_id' => $beban->semester_id,
            'hari_id' => $data['hari_id'],
            'sesi_mulai_id' => $sesi->id,
            'sesi_selesai_id' => $sesi->id,
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
            'html' => '<div class="badge bg-primary text-wrap text-start p-2 w-100 mb-1 shadow-sm"><div class="fw-bold fs-7">'.$beban->mataKuliah->nama_mk.'</div><small>'.$beban->dosen->nama_lengkap.' ('.$beban->kelas.')</small></div>'
        ]);
    }

    public function destroy(JadwalMengajar $jadwal)
    {
        $this->jadwalService->returnSksQuota($jadwal);
        $jadwal->delete();
        
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal kuliah berhasil dihapus dan kuota SKS telah dikembalikan.');
    }
}
