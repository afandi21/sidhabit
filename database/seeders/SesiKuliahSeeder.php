<?php

namespace Database\Seeders;

use App\Models\SesiKuliah;
use Illuminate\Database\Seeder;

class SesiKuliahSeeder extends Seeder
{
    public function run(): void
    {
        $sesi = [
            ['nama_sesi' => '1', 'jam_mulai' => '07:30:00', 'jam_selesai' => '08:15:00'],
            ['nama_sesi' => '2', 'jam_mulai' => '08:20:00', 'jam_selesai' => '09:05:00'],
            ['nama_sesi' => '3', 'jam_mulai' => '09:10:00', 'jam_selesai' => '09:55:00'],
            ['nama_sesi' => '4', 'jam_mulai' => '10:00:00', 'jam_selesai' => '10:45:00'],
            ['nama_sesi' => '5', 'jam_mulai' => '11:00:00', 'jam_selesai' => '11:45:00'],
            ['nama_sesi' => '6', 'jam_mulai' => '11:50:00', 'jam_selesai' => '12:35:00'],
            ['nama_sesi' => '7', 'jam_mulai' => '14:30:00', 'jam_selesai' => '15:15:00'],
            ['nama_sesi' => '8', 'jam_mulai' => '15:15:00', 'jam_selesai' => '16:00:00'],
        ];

        foreach ($sesi as $s) {
            SesiKuliah::firstOrCreate(['nama_sesi' => $s['nama_sesi']], $s);
        }
    }
}
