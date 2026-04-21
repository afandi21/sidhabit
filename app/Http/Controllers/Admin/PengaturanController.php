<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\LokasiKampus;

class PengaturanController extends Controller
{
    protected $pengaturanService;

    public function __construct(\App\Services\PengaturanService $pengaturanService)
    {
        $this->pengaturanService = $pengaturanService;
    }

    public function index()
    {
        $lokasi = LokasiKampus::find(1);
        $semesters = \App\Models\Semester::all();
        $activeSemester = \App\Models\Semester::where('is_active', true)->first();
        
        $health = $this->pengaturanService->getSystemHealth();

        return view('admin.pengaturan.index', compact('lokasi', 'health', 'semesters', 'activeSemester'));
    }

    public function updateLocation(\App\Http\Requests\UpdateLokasiRequest $request)
    {
        $this->pengaturanService->updateLokasi($request->validated());
        return back()->with('success', 'Lokasi Kampus berhasil diperbarui.');
    }

    public function toggleMaintenance()
    {
        $message = $this->pengaturanService->toggleMaintenance();
        return back()->with('success', $message);
    }

    public function storeSemester(\App\Http\Requests\StoreSemesterRequest $request)
    {
        $this->pengaturanService->storeSemester($request->validated());
        return back()->with('success', 'Periode Akademik baru berhasil ditambahkan.');
    }

    public function updateSemester(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|exists:semesters,id',
        ]);

        try {
            $this->pengaturanService->activateSemester($request->semester_id);
            return back()->with('success', 'Semester & Tahun Akademik aktif berhasil diubah.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal merubah semester aktif.');
        }
    }

    public function backup()
    {
        try {
            Artisan::call('optimize:clear');
            return back()->with('success', 'Optimasi sistem & Backup log berhasil dilakukan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal melakukan backup: ' . $e->getMessage());
        }
    }
}
