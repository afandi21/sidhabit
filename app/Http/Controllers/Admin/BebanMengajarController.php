<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BebanMengajar;
use App\Models\Semester;
use App\Models\Dosen;
use App\Models\MataKuliah;
use Illuminate\Http\Request;

class BebanMengajarController extends Controller
{
    public function index()
    {
        $query = BebanMengajar::with(['semester', 'dosen', 'mataKuliah.programStudi'])
            ->whereHas('semester', fn($q) => $q->where('is_active', true));

        // Filter Operator Prodi
        if (auth()->user()->isOperatorProdi()) {
            $query->whereHas('mataKuliah', function ($q) {
                $q->where('program_studi_id', auth()->user()->program_studi_id);
            });
        }

        $bebans = $query->orderBy('dosen_id')->get();
            
        return view('admin.beban.index', compact('bebans'));
    }

    public function create()
    {
        $semesters = Semester::where('is_active', true)->get();
        $dosens = Dosen::orderBy('nama_gelar')->get();
        
        $mkQuery = MataKuliah::with('programStudi')->where('is_active', true);
        if (auth()->user()->isOperatorProdi()) {
            $mkQuery->where('program_studi_id', auth()->user()->program_studi_id);
        }
        $matkuls = $mkQuery->orderBy('nama_mk')->get();
        
        return view('admin.beban.create', compact('semesters', 'dosens', 'matkuls'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|exists:semesters,id',
            'dosen_id' => 'required|exists:dosens,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'kelas' => 'required|string|max:10',
        ]);

        // Cek duplicate plotting
        $exists = BebanMengajar::where('semester_id', $request->semester_id)
            ->where('dosen_id', $request->dosen_id)
            ->where('mata_kuliah_id', $request->mata_kuliah_id)
            ->where('kelas', $request->kelas)
            ->exists();
            
        if ($exists) {
            return back()->with('error', 'Plotting dosen untuk mata kuliah dan kelas ini sudah ada.')->withInput();
        }

        $mk = MataKuliah::findOrFail($request->mata_kuliah_id);

        BebanMengajar::create([
            'semester_id' => $request->semester_id,
            'dosen_id' => $request->dosen_id,
            'mata_kuliah_id' => $request->mata_kuliah_id,
            'kelas' => $request->kelas,
            'total_sks' => $mk->sks,
            'sks_terjadwal' => 0
        ]);

        return redirect()->route('admin.beban.index')->with('success', 'Beban mengajar (Plotting) berhasil ditambahkan.');
    }

    public function destroy(BebanMengajar $beban)
    {
        if ($beban->sks_terjadwal > 0) {
            return back()->with('error', 'Gagal dihapus! Beban mengajar ini sudah memiliki jadwal aktif. Harap hapus jadwalnya terlebih dahulu.');
        }
        $beban->delete();
        return redirect()->route('admin.beban.index')->with('success', 'Plotting berhasil dihapus.');
    }
}
