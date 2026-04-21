<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRuanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ruangan = $this->route('ruangan');
        
        return [
            'kode_ruangan' => 'required|unique:ruangan,kode_ruangan,' . $ruangan->id,
            'nama_ruangan' => 'required|string|max:255',
            'lokasi_kampus_id' => 'required|exists:lokasi_kampus,id',
            'kapasitas' => 'nullable|integer',
        ];
    }
}
