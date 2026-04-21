<?php

namespace App\Imports;

use App\Models\Ruangan;
use App\Models\LokasiKampus;
use App\Traits\NormalizeImportTrait;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RuanganImport implements ToCollection, WithHeadingRow
{
    use NormalizeImportTrait;

    public function collection(Collection $rows)
    {
        // Ambil lokasi kampus utama sebagai default jika tidak ada
        $defaultKampus = LokasiKampus::first()?->id;

        foreach ($rows as $row) 
        {
            if (empty($row['kode_ruangan']) || empty($row['nama_ruangan'])) {
                continue;
            }

            Ruangan::updateOrCreate(
                ['kode_ruangan' => $row['kode_ruangan']],
                [
                    'nama_ruangan' => $row['nama_ruangan'],
                    'gedung' => $row['gedung'] ?? '-',
                    'lantai' => $row['lantai'] ?? 1,
                    'kapasitas' => $row['kapasitas'] ?? 40,
                    'lokasi_kampus_id' => $defaultKampus,
                    'is_active' => true,
                ]
            );
        }
    }
}
