<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\FoodItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $price = fake()->numberBetween(5000, 50000);

        return [
            'order_id' => Order::factory(),
            'food_item_id' => FoodItem::factory(),
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $quantity * $price,
        ];
    }
}
