<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\JadwalMengajar;
use App\Models\Presensi;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect('/')->with('error', 'Data dosen tidak ditemukan.');
        }

        $semesterAktif = Semester::where('is_active', true)->first();

        // Jadwal Mengajar Dosen (Seluruh Prodi)
        $jadwals = JadwalMengajar::with(['mataKuliah.programStudi', 'ruangan', 'hari', 'sesiMulai', 'sesiSelesai'])
            ->where('dosen_id', $dosen->id)
            ->where('semester_id', $semesterAktif->id ?? 0)
            ->orderBy('hari_id')
            ->orderBy('jam_mulai')
            ->get();

        // Statistik
        $totalHadir = Presensi::where('dosen_id', $dosen->id)
            ->where('status', 'hadir')
            ->whereHas('jadwalMengajar', fn($q) => $q->where('semester_id', $semesterAktif->id ?? 0))
            ->count();

        $jadwalHariIni = JadwalMengajar::with(['mataKuliah', 'ruangan', 'hari', 'sesiMulai'])
            ->where('dosen_id', $dosen->id)
            ->where('semester_id', $semesterAktif->id ?? 0)
            ->hariIni()
            ->get();

        return view('dosen.dashboard', compact('dosen', 'jadwals', 'totalHadir', 'jadwalHariIni', 'semesterAktif'));
    }
}
