<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', Rule::exists('patients', 'id')->whereNull('deleted_at')],
            'doctor_id' => ['required', 'exists:doctors,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['Unpaid', 'Paid', 'Pending'])],
            'bill_date' => ['required', 'date'],
        ];
    }
}
