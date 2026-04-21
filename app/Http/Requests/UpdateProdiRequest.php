<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProdiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $prodi = $this->route('prodi');
        $id = $prodi->id ?? $this->route('program_studi')->id ?? $this->route('id');

        return [
            'kode_prodi' => 'required|unique:program_studi,kode_prodi,' . $id,
            'nama_prodi' => 'required|string|max:255',
            'fakultas_id' => 'required|exists:fakultas,id',
            'jenjang' => 'required|string|max:10',
        ];
    }
}
