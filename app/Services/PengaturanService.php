<?php

namespace App\Services;

use App\Models\LokasiKampus;
use App\Models\Semester;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class PengaturanService
{
    /**
     * Mendapatkan data kesehatan sistem
     */
    public function getSystemHealth(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'os' => PHP_OS,
            'db_connection' => DB::connection()->getDatabaseName(),
            'is_maintenance' => app()->isDownForMaintenance(),
            'disk_free' => round(disk_free_space("/") / (1024 * 1024 * 1024), 2) . ' GB',
            'disk_total' => round(disk_total_space("/") / (1024 * 1024 * 1024), 2) . ' GB',
        ];
    }

    /**
     * Mengubah status maintenance aplikasi
     */
    public function toggleMaintenance(): string
    {
        if (app()->isDownForMaintenance()) {
            Artisan::call('up');
            return 'Sistem kembali Online.';
        } else {
            Artisan::call('down', ['--secret' => 'sidhabit-admin']);
            return 'Sistem masuk ke Mode Pemeliharaan. Gunakan secret "sidhabit-admin" untuk bypass.';
        }
    }

    /**
     * Memperbarui lokasi GPS Kampus
     */
    public function updateLokasi(array $data): void
    {
        LokasiKampus::updateOrCreate(
            ['id' => 1],
            [
                'nama_lokasi' => 'Kampus Utama',
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'radius_meter' => $data['radius_meter'],
                'is_active' => true
            ]
        );
    }

    /**
     * Menyimpan semester baru
     */
    public function storeSemester(array $data): void
    {
        Semester::create([
            'kode_semester' => $data['kode_semester'],
            'nama_semester' => $data['nama_semester'],
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'is_active' => false,
        ]);
    }

    /**
     * Mengaktifkan semester tertentu dan mematikan yang lain
     */
    public function activateSemester(int $semesterId): void
    {
        DB::beginTransaction();
        try {
            Semester::query()->update(['is_active' => false]);
            Semester::where('id', $semesterId)->update(['is_active' => true]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
