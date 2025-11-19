<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FoodCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Beverages', 'Snacks', 'Meals', 'Desserts']),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
