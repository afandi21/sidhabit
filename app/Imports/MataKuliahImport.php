<?php

namespace App\Imports;

use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MataKuliahImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            MataKuliah::updateOrCreate(
                ['kode_mk' => $row['kode_mk']],
                [
                    'nama_mk' => $row['nama_mk'],
                    'sks' => $row['sks'],
                    'semester' => $row['semester'],
                    'jenis' => strtolower($row['jenis']), // teori, praktikum
                    'kategori' => strtolower($row['kategori']), // dikti, mahad
                    'program_studi_id' => $this->getProdiId($row['prodi']),
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
