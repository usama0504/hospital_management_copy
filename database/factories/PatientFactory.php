<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('03#########'),
            'gender' => fake()->randomElement(['Male', 'Female', 'Other']),
            'dob' => fake()->date(),
            'address' => fake()->address(),
        ];
    }
}
