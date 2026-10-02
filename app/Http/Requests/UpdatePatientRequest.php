<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('patients', 'email')->ignore($this->route('patient'))],
            'phone' => ['required', 'string'],
            'address' => ['nullable', 'string'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'dob' => ['nullable', 'date'],
        ];
    }
}
