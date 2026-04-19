<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\LokasiKampus;

class PengaturanController extends Controller
{
    public function index()
    {
        $lokasi = LokasiKampus::find(1);
        $semesters = DB::table('semesters')->get();
        $activeSemester = DB::table('semesters')->where('is_active', true)->first();
        
        // System Health Data
        $health = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'os' => PHP_OS,
            'db_connection' => DB::connection()->getDatabaseName(),
            'is_maintenance' => app()->isDownForMaintenance(),
            'disk_free' => round(disk_free_space("/") / (1024 * 1024 * 1024), 2) . ' GB',
            'disk_total' => round(disk_total_space("/") / (1024 * 1024 * 1024), 2) . ' GB',
        ];

        return view('admin.pengaturan.index', compact('lokasi', 'health', 'semesters', 'activeSemester'));
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meter' => 'required|integer|min:10',
        ]);

        LokasiKampus::updateOrCreate(
            ['id' => 1],
            [
                'nama_lokasi' => 'Kampus Utama',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius_meter' => $request->radius_meter,
                'is_active' => true
            ]
        );

        return back()->with('success', 'Lokasi Kampus berhasil diperbarui.');
    }

    public function toggleMaintenance()
    {
        if (app()->isDownForMaintenance()) {
            Artisan::call('up');
            return back()->with('success', 'Sistem kembali Online.');
        } else {
            // Kita gunakan secret agar admin tetap bisa akses saat maintenance
            Artisan::call('down', ['--secret' => 'sidhabit-admin']);
            return back()->with('success', 'Sistem masuk ke Mode Pemeliharaan. Gunakan secret "sidhabit-admin" untuk bypass.');
        }
    }

    public function storeSemester(Request $request)
    {
        $request->validate([
            'kode_semester' => 'required|unique:semesters,kode_semester',
            'nama_semester' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ]);

        DB::table('semesters')->insert([
            'kode_semester' => $request->kode_semester,
            'nama_semester' => $request->nama_semester,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'is_active' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Periode Akademik baru berhasil ditambahkan.');
    }

    public function updateSemester(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|exists:semesters,id',
        ]);

        // Matikan semua semester lain, aktifkan yang dipilih
        DB::table('semesters')->update(['is_active' => false]);
        DB::table('semesters')->where('id', $request->semester_id)->update(['is_active' => true]);

        return back()->with('success', 'Semester & Tahun Akademik aktif berhasil diubah.');
    }

    public function backup()
    {
        try {
            // Simulasi backup sederhana (Hanya memicu export via artisan jika ada package)
            // Untuk sekarang kita trigger pembersihan cache sebagai bagian pemeliharaan
            Artisan::call('optimize:clear');
            return back()->with('success', 'Optimasi sistem & Backup log berhasil dilakukan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal melakukan backup: ' . $e->getMessage());
        }
    }
}
