<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    // Role check controller (aur route middleware) mein hota hai.
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:patients,email'],
            'phone' => ['required', 'string'],
            'address' => ['nullable', 'string'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'dob' => ['nullable', 'date'],
        ];
    }
}
