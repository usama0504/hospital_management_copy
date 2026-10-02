<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // whereNull('deleted_at'): delete kiye hue patient/appointment par bill nahi ban sakta
            'patient_id' => ['required', Rule::exists('patients', 'id')->whereNull('deleted_at')],
            'doctor_id' => ['required', 'exists:doctors,id'],
            'appointment_id' => ['nullable', Rule::exists('appointments', 'id')->whereNull('deleted_at')],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['Unpaid', 'Paid', 'Pending'])],
            'bill_date' => ['required', 'date'],
        ];
    }
}
