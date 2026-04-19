<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $dosen = Auth::user()->dosen;
        
        $query = Presensi::with(['jadwalMengajar.mataKuliah', 'jadwalMengajar.ruangan'])
            ->where('dosen_id', $dosen->id);

        // Filter by month
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $query->whereMonth('tanggal', $month)
              ->whereYear('tanggal', $year);

        $riwayats = $query->orderBy('tanggal', 'desc')
                          ->orderBy('jam_masuk', 'desc')
                          ->get();

        return view('dosen.riwayat', compact('riwayats', 'dosen', 'month', 'year'));
    }
}
