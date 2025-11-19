<?php

namespace Database\Seeders;

use App\Models\FoodItem;
use App\Models\FoodCategory;
use Illuminate\Database\Seeder;

class FoodItemSeeder extends Seeder
{
    public function run(): void
    {
        $beverages = FoodCategory::where('name', 'Beverages')->first();
        $snacks = FoodCategory::where('name', 'Snacks')->first();
        $meals = FoodCategory::where('name', 'Meals')->first();
        $desserts = FoodCategory::where('name', 'Desserts')->first();

        $items = [
            // Beverages
            ['category' => $beverages, 'name' => 'Coca Cola', 'price' => 5000, 'stock' => 100],
            ['category' => $beverages, 'name' => 'Sprite', 'price' => 5000, 'stock' => 100],
            ['category' => $beverages, 'name' => 'Orange Juice', 'price' => 8000, 'stock' => 50],
            ['category' => $beverages, 'name' => 'Mineral Water', 'price' => 3000, 'stock' => 150],
            ['category' => $beverages, 'name' => 'Iced Coffee', 'price' => 12000, 'stock' => 80],

            // Snacks
            ['category' => $snacks, 'name' => 'Potato Chips', 'price' => 10000, 'stock' => 200],
            ['category' => $snacks, 'name' => 'Popcorn', 'price' => 8000, 'stock' => 150],
            ['category' => $snacks, 'name' => 'Nachos', 'price' => 15000, 'stock' => 100],
            ['category' => $snacks, 'name' => 'French Fries', 'price' => 12000, 'stock' => 120],

            // Meals
            ['category' => $meals, 'name' => 'Burger', 'price' => 25000, 'stock' => 50],
            ['category' => $meals, 'name' => 'Pizza Slice', 'price' => 18000, 'stock' => 60],
            ['category' => $meals, 'name' => 'Fried Rice', 'price' => 20000, 'stock' => 40],
            ['category' => $meals, 'name' => 'Spaghetti', 'price' => 22000, 'stock' => 40],

            // Desserts
            ['category' => $desserts, 'name' => 'Ice Cream', 'price' => 10000, 'stock' => 80],
            ['category' => $desserts, 'name' => 'Chocolate Cake', 'price' => 15000, 'stock' => 30],
        ];

        foreach ($items as $item) {
            FoodItem::create([
                'food_category_id' => $item['category']->id,
                'name' => $item['name'],
                'price' => $item['price'],
                'stock' => $item['stock'],
                'is_active' => true,
            ]);
        }
    }
}
