<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'program_studi_id' => ['nullable', 'exists:program_studi,id'],
        ];

        if ($this->filled('password')) {
            $rules['password'] = ['confirmed', Rules\Password::defaults()];
        }

        return $rules;
    }
}
