<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\RentalSession;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(10000, 100000);
        $tax = $subtotal * 0.10;

        return [
            'rental_session_id' => fake()->optional(0.5)->randomElement(RentalSession::pluck('id')->toArray()),
            'user_id' => User::factory(),
            'order_number' => 'ORD-' . fake()->unique()->numerify('######'),
            'customer_name' => fake()->optional(0.7)->name(),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
            'status' => fake()->randomElement(['pending', 'completed']),
            'payment_status' => fake()->randomElement(['unpaid', 'paid']),
            'payment_method' => fake()->optional(0.7)->randomElement(['cash', 'card', 'transfer']),
        ];
    }
}
