<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFakultasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $fakultas = $this->route('fakulta'); // Mengikuti convention laravel di resource
        $id = $fakultas->id ?? $this->route('fakultas')->id ?? $this->route('id');

        return [
            'kode_fakultas' => 'required|unique:fakultas,kode_fakultas,' . $id,
            'nama_fakultas' => 'required|string|max:255',
        ];
    }
}
