<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => Doctor::factory(),
            'appointment_date' => now()->addDays(3)->setTime(10, 0),
            'status' => 'Pending',
            'notes' => null,
        ];
    }
}
