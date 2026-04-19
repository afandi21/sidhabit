<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HariSeeder extends Seeder
{
    public function run()
    {
        // Format: Sabtu - Kamis (Jumat Libur)
        $haris = [
            ['nama_hari' => 'Sabtu', 'urutan' => 1],
            ['nama_hari' => 'Ahad', 'urutan' => 2],
            ['nama_hari' => 'Senin', 'urutan' => 3],
            ['nama_hari' => 'Selasa', 'urutan' => 4],
            ['nama_hari' => 'Rabu', 'urutan' => 5],
            ['nama_hari' => 'Kamis', 'urutan' => 6],
        ];

        // Hapus data lama agar tidak duplikat dengan aman
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('hari')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        foreach ($haris as $hari) {
            DB::table('hari')->insert($hari);
        }
    }
}
