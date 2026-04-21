<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMataKuliahRequest extends FormRequest
{
    /**
     * Tentukan apakah user punya otorisasi untuk melakukan request ini.
     */
    public function authorize(): bool
    {
        return true; // Asumsi middleware Role sudah menangani otorisasi di routing
    }

    /**
     * Aturan validasi yang diterapkan.
     */
    public function rules(): array
    {
        return [
            'kode_mk' => 'required|unique:mata_kuliah,kode_mk',
            'nama_mk' => 'required|string|max:255',
            'sks' => 'required|integer|min:1|max:8',
            'semester' => 'required|integer|min:1|max:8',
            'program_studi_id' => 'required|exists:program_studi,id',
            'jenis' => 'required|in:teori,praktikum,teori_praktikum',
            'kategori' => 'required|in:dikti,mahad',
        ];
    }

    /**
     * (Opsional) Pesan error kustom untuk membantu user.
     */
    public function messages(): array
    {
        return [
            'kode_mk.unique' => 'Kode Mata Kuliah ini sudah digunakan, silakan gunakan kode lain.',
            'sks.max' => 'SKS maksimal yang diizinkan adalah 8.',
            'semester.max' => 'Semester maksimal adalah semester 8.',
        ];
    }
}
