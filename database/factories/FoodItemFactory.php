<?php

namespace Database\Factories;

use App\Models\FoodCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class FoodItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'food_category_id' => FoodCategory::factory(),
            'name' => fake()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'price' => fake()->numberBetween(5000, 50000),
            'stock' => fake()->numberBetween(0, 200),
            'image' => null,
            'is_active' => true,
        ];
    }
}
