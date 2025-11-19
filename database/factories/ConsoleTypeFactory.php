<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ConsoleTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['PS3', 'PS4', 'PS5', 'VR']),
            'hourly_rate' => fake()->numberBetween(15000, 35000),
            'is_active' => true,
        ];
    }
}
