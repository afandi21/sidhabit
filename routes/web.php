<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Dosen\PresensiController as DosenPresensi;

Route::get('/set-campus-location', function() {
    \App\Models\LokasiKampus::updateOrCreate(
        ['id' => 1],
        [
            'nama_lokasi' => 'Kampus STAI',
            'latitude' => 3.531231,
            'longitude' => 98.760735,
            'radius_meter' => 50,
            'is_active' => true
        ]
    );
    return "Lokasi Kampus Berhasil diatur ke 3.531231, 98.760735 dengan radius 50 Meter.";
});

Route::get('/debug-user', function() {
    if(!auth()->check()) return "Belum Login";
    $user = auth()->user();
    return [
        'name' => $user->name,
        'email' => $user->email,
        'roles' => $user->getRoleNames(),
        'all_roles_in_db' => \Illuminate\Support\Facades\DB::table('roles')->pluck('name')
    ];
});

Route::get('/fix-roles', function() {
    try {
        // 1. Ganti nama role di database (Gunakan DB query untuk menghindari fatal error model)
        $roleExists = \Illuminate\Support\Facades\DB::table('roles')->where('name', 'super_admin')->first();
        if($roleExists) {
            \Illuminate\Support\Facades\DB::table('roles')
                ->where('name', 'super_admin')
                ->update(['name' => 'wakil_1_akademik']);
        } else {
            \Illuminate\Support\Facades\DB::table('roles')->insertOrIgnore([
                'name' => 'wakil_1_akademik',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        // 2. Update info User Admin
        \Illuminate\Support\Facades\DB::table('users')
            ->where('email', 'admin@presensi.ac.id')
            ->update(['name' => 'Wakil 1 Akademik']);

        // Jika user sedang login, berikan role langsung
        if(auth()->check()) {
            auth()->user()->assignRole('wakil_1_akademik');
        }
        
        // 3. Clear Cache
        \Illuminate\Support\Facades\Artisan::call('permission:cache-reset');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        
        return "Sinkronisasi Berhasil via raw DB! Silakan LOGIN ULANG.";
    } catch (\Exception $e) {
        return "Terjadi kesalahan: " . $e->getMessage();
    }
});

Route::get('/fix-webauthn-table', function() {
    try {
        // 1. Hapus tabel lama yang bermasalah
        \Illuminate\Support\Facades\Schema::dropIfExists('webauthn_credentials');
        
        // 2. Buat ulang menggunakan schema bawaan asli dari package
        \Laragear\WebAuthn\Models\WebAuthnCredential::migration()->up();
        
        return "Tabel webauthn_credentials BERHASIL direset sesuai standar terbaru! Silakan kembali dan coba daftar sidik jari lagi.";
    } catch (\Exception $e) {
        return "Terjadi kesalahan: " . $e->getMessage();
    }
});

Route::get('/', function () { 
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->hasRole('wakil_1_akademik') || $user->hasRole('admin_fakultas')) {
            return redirect('/admin/dashboard');
        } elseif ($user->hasRole('kaprodi')) {
            return redirect('/kaprodi/dashboard');
        } else {
            return redirect('/dosen/dashboard');
        }
    }
    return redirect('/login'); 
});

// WebAuthn Routes (Login & Register) - Didefinisikan Manual
Route::post('webauthn/register/options', [\App\Http\Controllers\WebAuthn\WebAuthnRegisterController::class, 'options'])->name('webauthn.register.options')->middleware('web');
Route::post('webauthn/register', [\App\Http\Controllers\WebAuthn\WebAuthnRegisterController::class, 'register'])->name('webauthn.register')->middleware('web');

Route::post('webauthn/login/options', [\App\Http\Controllers\WebAuthn\WebAuthnLoginController::class, 'options'])->name('webauthn.login.options')->middleware('web');
Route::post('webauthn/login', [\App\Http\Controllers\WebAuthn\WebAuthnLoginController::class, 'login'])->name('webauthn.login')->middleware('web');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
});

// Auth Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::prefix('admin')->middleware('role:wakil_1_akademik,admin_fakultas,kaprodi')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('admin.dashboard');
        Route::resource('fakultas', \App\Http\Controllers\Admin\FakultasController::class, ['as' => 'admin']);
        Route::resource('prodi', \App\Http\Controllers\Admin\ProdiController::class, ['as' => 'admin']);
        Route::resource('matakuliah', \App\Http\Controllers\Admin\MataKuliahController::class, ['as' => 'admin'])->except(['show']);
        Route::post('matakuliah/import', [\App\Http\Controllers\Admin\MataKuliahController::class, 'import'])->name('admin.matakuliah.import');
        Route::get('matakuliah/template', [\App\Http\Controllers\Admin\MataKuliahController::class, 'downloadTemplate'])->name('admin.matakuliah.template');
        Route::resource('ruangan', \App\Http\Controllers\Admin\RuanganController::class, ['as' => 'admin']);
        Route::resource('sesikuliah', \App\Http\Controllers\Admin\SesiKuliahController::class, ['as' => 'admin']);
        Route::resource('dosen', \App\Http\Controllers\Admin\DosenController::class, ['as' => 'admin'])->except(['show']);
        Route::post('dosen/import', [\App\Http\Controllers\Admin\DosenController::class, 'import'])->name('admin.dosen.import');
        Route::get('dosen/template', [\App\Http\Controllers\Admin\DosenController::class, 'downloadTemplate'])->name('admin.dosen.template');
        Route::get('dosen/{dosen}/impersonate', [\App\Http\Controllers\Admin\DosenController::class, 'loginAsDosen'])->name('admin.dosen.impersonate');
        Route::get('dosen/{dosen}/ketersediaan', [\App\Http\Controllers\Admin\DosenAvailabilityController::class, 'show'])->name('admin.dosen.ketersediaan');
        Route::post('dosen/{dosen}/ketersediaan', [\App\Http\Controllers\Admin\DosenAvailabilityController::class, 'update'])->name('admin.dosen.ketersediaan.update');
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class, ['as' => 'admin']);
        Route::get('jadwal/board', [\App\Http\Controllers\Admin\JadwalController::class, 'board'])->name('admin.jadwal.board');
        Route::post('jadwal/board/drop', [\App\Http\Controllers\Admin\JadwalController::class, 'boardDrop'])->name('admin.jadwal.board.drop');
        Route::post('jadwal/generate', [\App\Http\Controllers\Admin\JadwalController::class, 'autoGenerate'])->name('admin.jadwal.generate');
        Route::resource('jadwal', \App\Http\Controllers\Admin\JadwalController::class, ['as' => 'admin']);
        Route::resource('beban', \App\Http\Controllers\Admin\BebanMengajarController::class, ['as' => 'admin']);
        Route::prefix('laporan')->group(function () {
            Route::get('/harian', [\App\Http\Controllers\Admin\LaporanController::class, 'harian'])->name('admin.laporan.harian');
            Route::get('/bulanan', [\App\Http\Controllers\Admin\LaporanController::class, 'bulanan'])->name('admin.laporan.bulanan');
            Route::get('/bulanan/excel', [\App\Http\Controllers\Admin\LaporanController::class, 'exportExcel'])->name('admin.laporan.bulanan.excel');
            Route::get('/bulanan/pdf', [\App\Http\Controllers\Admin\LaporanController::class, 'exportPdf'])->name('admin.laporan.bulanan.pdf');
        });

        // Izin & Cuti
        Route::get('/izin-cuti', [\App\Http\Controllers\Admin\IzinCutiController::class, 'index'])->name('admin.izin.index');
        Route::post('/izin-cuti/{id}/approve', [\App\Http\Controllers\Admin\IzinCutiController::class, 'approve'])->name('admin.izin.approve');
        Route::post('/izin-cuti/{id}/reject', [\App\Http\Controllers\Admin\IzinCutiController::class, 'reject'])->name('admin.izin.reject');

        // Pengaturan Sistem (HANYA UNTUK WAKIL 1 / SUPERADMIN)
        Route::middleware('role:wakil_1_akademik')->group(function () {
            Route::get('/pengaturan', [\App\Http\Controllers\Admin\PengaturanController::class, 'index'])->name('admin.pengaturan.index');
            Route::post('/pengaturan/location', [\App\Http\Controllers\Admin\PengaturanController::class, 'updateLocation'])->name('admin.pengaturan.location');
            Route::post('/pengaturan/semester/store', [\App\Http\Controllers\Admin\PengaturanController::class, 'storeSemester'])->name('admin.pengaturan.semester.store');
            Route::post('/pengaturan/semester', [\App\Http\Controllers\Admin\PengaturanController::class, 'updateSemester'])->name('admin.pengaturan.semester');
            Route::post('/pengaturan/maintenance', [\App\Http\Controllers\Admin\PengaturanController::class, 'toggleMaintenance'])->name('admin.pengaturan.maintenance');
            Route::post('/pengaturan/backup', [\App\Http\Controllers\Admin\PengaturanController::class, 'backup'])->name('admin.pengaturan.backup');
        });
    });

    Route::prefix('dosen')->middleware('role:dosen')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Dosen\DashboardController::class, 'index'])->name('dosen.dashboard');
        Route::get('/presensi', [DosenPresensi::class, 'index'])->name('dosen.presensi');
        Route::get('/riwayat', [\App\Http\Controllers\Dosen\RiwayatController::class, 'index'])->name('dosen.riwayat');
        Route::post('/presensi/init', [DosenPresensi::class, 'init']);
        Route::post('/presensi/verify', [DosenPresensi::class, 'verify']);
        
        // Device Management (Fingerprint)
        Route::resource('device', \App\Http\Controllers\Dosen\DeviceController::class);
        
        // Availability Management
        Route::get('/availability', [\App\Http\Controllers\Dosen\AvailabilityController::class, 'index'])->name('dosen.availability');
        Route::post('/availability', [\App\Http\Controllers\Dosen\AvailabilityController::class, 'store'])->name('dosen.availability.store');
    });

    Route::prefix('kaprodi')->middleware('role:kaprodi')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Kaprodi\DashboardController::class, 'index'])->name('kaprodi.dashboard');
    });
});
