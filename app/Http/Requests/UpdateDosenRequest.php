<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDosenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $dosen = $this->route('dosen');
        
        return [
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $dosen->user_id,
            'nidn' => 'nullable|unique:dosens,nidn,' . $dosen->id,
            'nuptk' => 'nullable|unique:dosens,nuptk,' . $dosen->id,
            'program_studi_id' => 'required|exists:program_studi,id',
            'password' => 'nullable|min:8',
        ];
    }
}
