<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::factory(),
            'patient_name' => fake()->name(),
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->sentence(),
            'is_approved' => true,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['is_approved' => false]);
    }
}
