<?php

namespace App\Imports;

use App\Models\Dosen;
use App\Models\User;
use App\Traits\NormalizeImportTrait;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DosenImport implements ToCollection, WithHeadingRow
{
    use NormalizeImportTrait;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            // Skip jika email atau nama kosong
            if (empty($row['email']) || empty($row['nama_lengkap'])) {
                continue;
            }

            // Handle format tanggal
            $rawDate = $row['tanggal_lahir'] ?? null;
            try {
                $tglLahir = is_numeric($rawDate) 
                    ? \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawDate))
                    : \Carbon\Carbon::parse(str_replace('/', '-', $rawDate));
            } catch (\Exception $e) {
                $tglLahir = \Carbon\Carbon::parse('1970-01-01');
            }
            
            $prodiId = $this->getProdiId($this->getColumnValue($row, ['prodi', 'program_studi']));

            // 1. Cari atau buat User
            $user = User::updateOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['nama_lengkap'],
                    'username' => $row['nidn'] ?? Str::before($row['email'], '@'),
                    'password' => Hash::make($tglLahir->format('dmY')),
                    'program_studi_id' => $prodiId,
                ]
            );

            if (!$user->hasRole('dosen')) $user->assignRole('dosen');

            // 2. Parsing Nama dan Gelar secara Otomatis
            $fullRawName = $row['nama_lengkap_beserta_gelar'] ?? $row['nama_lengkap'];
            $gelarDepan = null;
            $gelarBelakang = null;
            $namaMurni = $row['nama_lengkap'];

            if (str_contains($fullRawName, ',')) {
                $parts = explode(',', $fullRawName);
                $namaMurniWithGelarDepan = trim($parts[0]);
                $gelarBelakang = trim($parts[1] ?? '');
                
                $nameParts = explode(' ', $namaMurniWithGelarDepan);
                if (count($nameParts) > 1 && (str_contains($nameParts[0], '.') || strlen($nameParts[0]) <= 3)) {
                    $gelarDepan = $nameParts[0];
                    array_shift($nameParts);
                    $namaMurni = implode(' ', $nameParts);
                } else {
                    $namaMurni = $namaMurniWithGelarDepan;
                }
            }

            // 3. Simpan Profil Dosen
            Dosen::updateOrCreate(
                !empty($row['nidn']) ? ['nidn' => $row['nidn']] : ['user_id' => $user->id],
                [
                    'user_id' => $user->id,
                    'nidn' => $row['nidn'] ?? null,
                    'nuptk' => $row['nuptk'] ?? null,
                    'nama_lengkap' => $namaMurni,
                    'gelar_depan' => $gelarDepan,
                    'gelar_belakang' => $gelarBelakang,
                    'program_studi_id' => $prodiId,
                    'jenis_kelamin' => (isset($row['jenis_kelamin']) && strtolower($row['jenis_kelamin']) == 'l') ? 'L' : 'P',
                    'tanggal_lahir' => $tglLahir->format('Y-m-d'),
                    'no_hp' => $row['no_hp'] ?? null,
                    'is_active' => true,
                ]
            );
        }
    }
}
