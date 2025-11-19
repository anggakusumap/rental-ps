<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true) . ' Package',
            'duration_minutes' => fake()->randomElement([60, 120, 180, 240]),
            'price' => fake()->numberBetween(15000, 50000),
            'is_active' => true,
        ];
    }
}
