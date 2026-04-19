<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\Presensi;
use App\Models\JadwalMengajar;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $prodiId = $user->program_studi_id;

        if (!$prodiId) {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak ditugaskan ke Program Studi manapun.');
        }

        $prodi = ProgramStudi::find($prodiId);

        // Stats
        $stats = [
            'total_dosen' => Dosen::where('program_studi_id', $prodiId)->count(),
            'total_mk' => MataKuliah::where('program_studi_id', $prodiId)->count(),
            'total_jadwal' => JadwalMengajar::whereHas('mataKuliah', fn($q) => $q->where('program_studi_id', $prodiId))->count(),
            'hadir_hari_ini' => Presensi::where('tanggal', today())
                ->whereHas('jadwalMengajar.mataKuliah', fn($q) => $q->where('program_studi_id', $prodiId))
                ->count(),
        ];

        // Keaktifan Dosen (Top 5)
        $dosenKeaktifan = Dosen::where('program_studi_id', $prodiId)
            ->withCount(['presensi' => function($q) {
                $q->where('status', 'hadir');
            }])
            ->orderBy('presensi_count', 'desc')
            ->take(5)
            ->get();

        // Jadwal Berjalan Saat Ini
        $jadwalBerjalan = JadwalMengajar::with(['dosen', 'mataKuliah', 'ruangan'])
            ->whereHas('mataKuliah', fn($q) => $q->where('program_studi_id', $prodiId))
            ->whereHas('hari', fn($q) => $q->where('nama_hari', now()->isoFormat('dddd')))
            // Filter jam (opsional, untuk kesederhanaan kita tampilkan semua hari ini)
            ->get();

        return view('kaprodi.dashboard', compact('prodi', 'stats', 'dosenKeaktifan', 'jadwalBerjalan'));
    }
}
