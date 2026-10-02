<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePrescriptionRequest extends FormRequest
{
    // Doctor/admin ka access route middleware aur controller (authorizeAccess) mein check hota hai.
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', Rule::exists('patients', 'id')->whereNull('deleted_at')],
            'appointment_id' => ['nullable', Rule::exists('appointments', 'id')->whereNull('deleted_at')],
            'prescribed_date' => ['required', 'date'],
            'diagnosis' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medicine_name' => ['required', 'string'],
            'items.*.dosage' => ['nullable', 'string'],
            'items.*.frequency' => ['nullable', 'string'],
            'items.*.duration' => ['nullable', 'string'],
            'items.*.instructions' => ['nullable', 'string'],
        ];
    }
}
