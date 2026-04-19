<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bebans = \App\Models\BebanMengajar::all();
foreach ($bebans as $b) {
    // Cari semua jadwal utk beban ini
    $jadwals = \App\Models\JadwalMengajar::where('semester_id', $b->semester_id)
        ->where('dosen_id', $b->dosen_id)
        ->where('mata_kuliah_id', $b->mata_kuliah_id)
        ->where('kelas', $b->kelas)
        ->get();
        
    $terjadwal = 0;
    foreach ($jadwals as $j) {
        $terjadwal += \App\Models\SesiKuliah::where('id', '>=', $j->sesi_mulai_id)
            ->where('id', '<=', $j->sesi_selesai_id)
            ->count() ?: 1;
    }
    
    $b->sks_terjadwal = $terjadwal;
    $b->save();
}
echo "SKS disinkronisasi ulang.";
