<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hari;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\Semester;
use App\Models\LokasiKampus;
use App\Models\Pengaturan;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === ROLES ===
        $roles = ['wakil_1_akademik', 'admin_fakultas', 'kaprodi', 'dosen'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // === HARI ===
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        foreach ($hariList as $i => $nama) {
            Hari::firstOrCreate(['nama_hari' => $nama, 'urutan' => $i + 1]);
        }

        // === FAKULTAS & PRODI SAMPLE ===
        $fti = Fakultas::firstOrCreate(
            ['kode_fakultas' => 'FTI'],
            ['nama_fakultas' => 'Fakultas Teknologi Informasi', 'dekan' => 'Dr. Contoh, M.Kom']
        );
        ProgramStudi::firstOrCreate(
            ['kode_prodi' => 'TI'],
            ['fakultas_id' => $fti->id, 'nama_prodi' => 'Teknik Informatika', 'jenjang' => 'S1']
        );
        ProgramStudi::firstOrCreate(
            ['kode_prodi' => 'SI'],
            ['fakultas_id' => $fti->id, 'nama_prodi' => 'Sistem Informasi', 'jenjang' => 'S1']
        );

        $feb = Fakultas::firstOrCreate(
            ['kode_fakultas' => 'FEB'],
            ['nama_fakultas' => 'Fakultas Ekonomi dan Bisnis', 'dekan' => 'Dr. Ekonomi, M.M']
        );
        ProgramStudi::firstOrCreate(
            ['kode_prodi' => 'MN'],
            ['fakultas_id' => $feb->id, 'nama_prodi' => 'Manajemen', 'jenjang' => 'S1']
        );

        // === SEMESTER ===
        Semester::firstOrCreate(
            ['kode_semester' => '20252'],
            [
                'nama_semester' => 'Genap 2025/2026',
                'tanggal_mulai' => '2026-02-01',
                'tanggal_selesai' => '2026-07-31',
                'is_active' => true,
            ]
        );

        // === LOKASI KAMPUS (sample - Jakarta) ===
        LokasiKampus::firstOrCreate(
            ['nama_lokasi' => 'Kampus Utama'],
            [
                'alamat' => 'Jl. Contoh No.1, Jakarta',
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'radius_meter' => 200,
            ]
        );

        // === PENGATURAN ===
        $settings = [
            ['key' => 'toleransi_terlambat', 'value' => '15', 'group' => 'presensi', 'description' => 'Toleransi keterlambatan (menit)'],
            ['key' => 'radius_gps', 'value' => '200', 'group' => 'presensi', 'description' => 'Radius GPS validasi (meter)'],
            ['key' => 'max_device', 'value' => '2', 'group' => 'presensi', 'description' => 'Maksimal device per dosen'],
            ['key' => 'nama_institusi', 'value' => 'Universitas Contoh', 'group' => 'general', 'description' => 'Nama institusi'],
        ];
        foreach ($settings as $s) {
            Pengaturan::firstOrCreate(['key' => $s['key']], $s);
        }

        // === WAKIL 1 AKADEMIK USER ===
        $admin = User::firstOrCreate(
            ['email' => 'admin@presensi.ac.id'],
            [
                'name' => 'Wakil 1 Akademik',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('wakil_1_akademik');

        // === SAMPLE DOSEN USER ===
        $dosenUser = User::firstOrCreate(
            ['email' => 'dosen@presensi.ac.id'],
            [
                'name' => 'Dr. Ahmad Fauzi',
                'password' => Hash::make('password'),
            ]
        );
        $dosenUser->assignRole('dosen');
        
        $prodi = ProgramStudi::where('kode_prodi', 'TI')->first();

        // === SAMPLE DOSEN DATA ===
        $dosen = \App\Models\Dosen::firstOrCreate(
            ['user_id' => $dosenUser->id],
            [
                'nidn' => '0101018001',
                'nama_lengkap' => 'Ahmad Fauzi',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => 'M.Kom',
                'jenis_kelamin' => 'L',
                'program_studi_id' => $prodi?->id,
                'status_aktif' => 'aktif',
            ]
        );

        // === MATA KULIAH SAMPLE ===
        $mk1 = \App\Models\MataKuliah::firstOrCreate(
            ['kode_mk' => 'MK001'],
            ['program_studi_id' => $prodi?->id, 'nama_mk' => 'Pemrograman Web', 'sks' => 3, 'jenis' => 'teori_praktikum']
        );
        $mk2 = \App\Models\MataKuliah::firstOrCreate(
            ['kode_mk' => 'MK002'],
            ['program_studi_id' => $prodi?->id, 'nama_mk' => 'Basis Data', 'sks' => 4, 'jenis' => 'teori_praktikum']
        );

        // === RUANGAN SAMPLE ===
        $kampus = LokasiKampus::first()->id;
        $r1 = \App\Models\Ruangan::firstOrCreate(
            ['kode_ruangan' => 'R301'],
            ['nama_ruangan' => 'Ruang Teori 301', 'gedung' => 'Gedung A', 'lantai' => 3, 'lokasi_kampus_id' => $kampus]
        );
        $r2 = \App\Models\Ruangan::firstOrCreate(
            ['kode_ruangan' => 'L102'],
            ['nama_ruangan' => 'Lab Komputer 102', 'gedung' => 'Gedung B', 'lantai' => 1, 'lokasi_kampus_id' => $kampus]
        );

        // === JADWAL SAMPLE ===
        $hariSelasa = Hari::where('nama_hari', 'Selasa')->first();
        $hariRabu = Hari::where('nama_hari', 'Rabu')->first();
        $semester = Semester::where('is_active', true)->first();

        \App\Models\JadwalMengajar::firstOrCreate(
            ['dosen_id' => $dosen->id, 'hari_id' => $hariSelasa->id, 'jam_mulai' => '08:00:00'],
            [
                'mata_kuliah_id' => $mk1->id,
                'ruangan_id' => $r1->id,
                'semester_id' => $semester->id,
                'jam_selesai' => '10:30:00',
                'kelas' => 'TI-A'
            ]
        );

        \App\Models\JadwalMengajar::firstOrCreate(
            ['dosen_id' => $dosen->id, 'hari_id' => $hariRabu->id, 'jam_mulai' => '10:00:00'],
            [
                'mata_kuliah_id' => $mk2->id,
                'ruangan_id' => $r2->id,
                'semester_id' => $semester->id,
                'jam_selesai' => '13:00:00',
                'kelas' => 'TI-B'
            ]
        );
    }
}
