<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class BillFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => Doctor::factory(),
            'appointment_id' => null,
            'amount' => fake()->randomFloat(2, 500, 5000),
            'status' => 'Unpaid',
            'bill_date' => now()->toDateString(),
        ];
    }
}
