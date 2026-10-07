<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('03#########'),
            'gender' => fake()->randomElement(['Male', 'Female']),
            'specialization' => fake()->randomElement(['Cardiology', 'Dermatology', 'General Medicine']),
            'consultation_fee' => 1000,
            'department_id' => Department::factory(),
        ];
    }
}
