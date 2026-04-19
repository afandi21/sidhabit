<?php

namespace App\Imports;

use App\Models\Dosen;
use App\Models\User;
use App\Models\ProgramStudi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DosenImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            // Format tanggal lahir untuk password (DDMMYYYY)
            // Asumsi format di Excel adalah YYYY-MM-DD atau sesuai format tanggal PHP
            $tglLahir = \Carbon\Carbon::parse($row['tanggal_lahir']);
            $passwordDefault = $tglLahir->format('dmY');

            // 1. Cari atau buat User
            $user = User::updateOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['nama_lengkap'],
                    'username' => $row['nidn'],
                    'password' => Hash::make($passwordDefault),
                    'program_studi_id' => $this->getProdiId($row['prodi']),
                ]
            );

            // Assign role dosen
            if (!$user->hasRole('dosen')) {
                $user->assignRole('dosen');
            }

            // 2. Simpan Data Dosen
            Dosen::updateOrCreate(
                ['nidn' => $row['nidn']],
                [
                    'user_id' => $user->id,
                    'nama_lengkap' => $row['nama_lengkap'],
                    'nama_gelar' => $row['nama_lengkap_beserta_gelar'],
                    'program_studi_id' => $this->getProdiId($row['prodi']),
                    'jenis_kelamin' => strtolower($row['jenis_kelamin']) == 'l' ? 'L' : 'P',
                    'tanggal_lahir' => $tglLahir->format('Y-m-d'),
                    'no_hp' => $row['no_hp'],
                    'is_active' => true,
                ]
            );
        }
    }

    private function getProdiId($namaProdi)
    {
        $prodi = ProgramStudi::where('nama_prodi', 'LIKE', '%' . $namaProdi . '%')->first();
        return $prodi ? $prodi->id : null;
    }
}
