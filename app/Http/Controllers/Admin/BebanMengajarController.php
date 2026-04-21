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
            ->whereHas('semester', fn($q) => $q->where('is_active', true))
            ->byRole('program_studi_id', 'mataKuliah');

        $bebans = $query->orderBy('dosen_id')->get();
            
        return view('admin.beban.index', compact('bebans'));
    }

    public function create()
    {
        $user = auth()->user();
        $semesters = Semester::where('is_active', true)->get();
        $dosens = Dosen::active()->orderBy('nama_lengkap')->byRole()->get();
        
        // Ambil ruangan, filter berdasarkan kode prodi jika operator prodi
        $ruangans = \App\Models\Ruangan::active()
            ->when($user->isOperatorProdi(), function($q) use ($user) {
                $kodeProdi = $user->programStudi->kode_prodi;
                return $q->where(function($sq) use ($kodeProdi) {
                    $sq->where('nama_ruangan', 'LIKE', "%{$kodeProdi}%")
                       ->orWhere('kode_ruangan', 'LIKE', "%{$kodeProdi}%");
                });
            })->get();
        
        $matkuls = MataKuliah::with('programStudi')
            ->active()
            ->byRole()
            ->orderBy('nama_mk')
            ->get();
        
        return view('admin.beban.create', compact('semesters', 'dosens', 'matkuls', 'ruangans'));
    }

    public function store(\App\Http\Requests\StoreBebanMengajarRequest $request)
    {
        $data = $request->validated();
        $mk = MataKuliah::findOrFail($data['mata_kuliah_id']);

        BebanMengajar::create([
            'semester_id' => $data['semester_id'],
            'dosen_id' => $data['dosen_id'],
            'mata_kuliah_id' => $data['mata_kuliah_id'],
            'kelas' => $data['kelas'],
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
