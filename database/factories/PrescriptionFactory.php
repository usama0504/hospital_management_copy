<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrescriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => Doctor::factory(),
            'appointment_id' => null,
            'prescribed_date' => now()->toDateString(),
            'diagnosis' => fake()->sentence(3),
            'notes' => null,
        ];
    }
}
