<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBebanMengajarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'semester_id' => 'required|exists:semesters,id',
            'dosen_id' => 'required|exists:dosens,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'kelas' => [
                'required',
                'string',
                'max:10',
                // Pengecekan Plotting Kembar (Duplicate Plotting)
                Rule::unique('beban_mengajar')->where(function ($query) {
                    return $query->where('semester_id', $this->semester_id)
                                 ->where('dosen_id', $this->dosen_id)
                                 ->where('mata_kuliah_id', $this->mata_kuliah_id);
                })
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'kelas.unique' => 'Plotting dosen untuk mata kuliah dan kelas ini sudah ada.',
        ];
    }
}
