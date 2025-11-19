<?php

namespace Database\Seeders;

use App\Models\FoodCategory;
use Illuminate\Database\Seeder;

class FoodCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Beverages', 'description' => 'Drinks and refreshments'],
            ['name' => 'Snacks', 'description' => 'Quick bites and munchies'],
            ['name' => 'Meals', 'description' => 'Full meals and dishes'],
            ['name' => 'Desserts', 'description' => 'Sweet treats'],
        ];

        foreach ($categories as $category) {
            FoodCategory::create($category);
        }
    }
}
