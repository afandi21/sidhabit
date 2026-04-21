<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMataKuliahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Mendapatkan ID mata kuliah dari route parameter untuk di-ignore di validasi unique
        $matakuliahId = $this->route('matakuliah')->id;

        return [
            'kode_mk' => 'required|unique:mata_kuliah,kode_mk,' . $matakuliahId,
            'nama_mk' => 'required|string|max:255',
            'sks' => 'required|integer|min:1|max:8',
            'semester' => 'required|integer|min:1|max:8',
            'program_studi_id' => 'required|exists:program_studi,id',
            'jenis' => 'required|in:teori,praktikum,teori_praktikum',
            'kategori' => 'required|in:dikti,mahad',
        ];
    }
}
