<?php

namespace App\Traits;

use App\Models\ProgramStudi;

trait NormalizeImportTrait
{
    /**
     * Mencari ID Prodi dengan logika fuzzy matching dan normalisasi karakter.
     */
    protected function getProdiId($input)
    {
        if (empty($input)) return null;

        $input = trim($input);
        $cleanInput = str_replace(["'", "’", "`"], "", $input);

        // Cari berdasarkan nama prodi (normalized) atau kode prodi
        return ProgramStudi::whereRaw("REPLACE(REPLACE(REPLACE(nama_prodi, \"'\", ''), '’', ''), '`', '') LIKE ?", ["%{$cleanInput}%"])
            ->orWhere('kode_prodi', 'LIKE', "%{$input}%")
            ->value('id');
    }

    /**
     * Mendeteksi nilai kolom secara agresif dari heading excel.
     */
    protected function getColumnValue($row, $searchTerms)
    {
        foreach ($row as $key => $val) {
            foreach ($searchTerms as $term) {
                if (str_contains(strtolower($key), strtolower($term))) {
                    return $val;
                }
            }
        }
        return null;
    }
}
