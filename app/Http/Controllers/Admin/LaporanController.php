<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    protected $laporanService;

    public function __construct(\App\Services\LaporanService $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    public function harian(Request $request)
    {
        $tanggal = $request->get('tanggal', today()->format('Y-m-d'));
        $prodiId = $request->get('program_studi_id');
        
        $query = Presensi::with(['dosen.programStudi', 'jadwalMengajar.mataKuliah', 'jadwalMengajar.ruangan'])
            ->whereDate('tanggal', $tanggal);

        $query = $this->laporanService->filterByRoleAndProdi($query, $prodiId);

        $presensis = $query->orderBy('jam_masuk', 'asc')->get();
        $prodis = ProgramStudi::active()->get();

        return view('admin.laporan.harian', compact('presensis', 'prodis', 'tanggal', 'prodiId'));
    }

    public function bulanan(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $prodiId = $request->get('program_studi_id');

        $query = Presensi::with('dosen')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        $query = $this->laporanService->filterByRoleAndProdi($query, $prodiId);
        
        $presensis = $query->get();
        $rekap = $this->laporanService->calculateRekapBulanan($presensis);
        $prodis = ProgramStudi::active()->get();

        return view('admin.laporan.bulanan', compact('rekap', 'prodis', 'bulan', 'tahun', 'prodiId'));
    }

    public function exportExcel(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $prodiId = $request->get('program_studi_id');

        $fileName = 'Rekap_Presensi_' . $bulan . '_' . $tahun . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\RekapPresensiExport($prodiId, $bulan, $tahun), 
            $fileName
        );
    }

    public function exportPdf(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $prodiId = $request->get('program_studi_id');

        $query = \App\Models\Dosen::with(['programStudi']);
        $query = $this->laporanService->filterDosenByRoleAndProdi($query, $prodiId);
        
        $dosens = $query->get();

        $data = [
            'dosens' => $dosens,
            'bulan' => \Carbon\Carbon::create()->month($bulan)->isoFormat('MMMM'),
            'tahun' => $tahun,
            'prodi' => $prodiId ? ProgramStudi::find($prodiId)->nama_prodi : 'Semua Program Studi'
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.laporan.pdf_rekap', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('Rekap_Presensi_' . $bulan . '_' . $tahun . '.pdf');
    }
}
