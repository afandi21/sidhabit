<?php

namespace App\Imports;

use App\Models\MataKuliah;
use App\Traits\NormalizeImportTrait;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MataKuliahImport implements ToCollection, WithHeadingRow
{
    use NormalizeImportTrait;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            // Skip jika data utama kosong
            if (empty($row['kode_mk']) || empty($row['nama_mk'])) {
                continue;
            }

            MataKuliah::updateOrCreate(
                ['kode_mk' => $row['kode_mk']],
                [
                    'nama_mk' => $row['nama_mk'],
                    'sks' => $row['sks'] ?? 2,
                    'semester' => $row['semester'] ?? 1,
                    'jenis' => strtolower($row['jenis'] ?? 'teori'),
                    'kategori' => strtolower($row['kategori'] ?? 'dikti'),
                    'program_studi_id' => $this->getProdiId($this->getColumnValue($row, ['prodi', 'program_studi'])),
                    'is_active' => true,
                ]
            );
        }
    }
}
