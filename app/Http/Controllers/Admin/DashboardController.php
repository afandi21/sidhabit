<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PresensiService;
use App\Models\Presensi;
use App\Models\Dosen;

class DashboardController extends Controller
{
    protected $presensiService;

    public function __construct(PresensiService $presensiService)
    {
        $this->presensiService = $presensiService;
    }

    public function index()
    {
        $stats = $this->presensiService->getStatistikHariIni();
        
        $presensiTerbaru = Presensi::with(['dosen', 'jadwalMengajar.mataKuliah', 'jadwalMengajar.ruangan'])
            ->hariIni()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $dosenBelumHadir = Dosen::aktif()
            ->whereDoesntHave('presensi', function($q) {
                $q->hariIni()->whereIn('status', ['hadir', 'terlambat']);
            })
            ->limit(5)
            ->get();

        // Statistik untuk Chart (7 hari terakhir)
        $chartData = [];
        for($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartData['labels'][] = now()->subDays($i)->isoFormat('dddd');
            $chartData['data'][] = Presensi::whereDate('tanggal', $date)->count();
        }

        return view('admin.dashboard', compact('stats', 'presensiTerbaru', 'dosenBelumHadir', 'chartData'));
    }
}
