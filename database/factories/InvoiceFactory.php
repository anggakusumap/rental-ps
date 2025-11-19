<?php

amespace Database\Factories;

use App\Models\User;
use App\Models\RentalSession;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $consoleCharges = fake()->numberBetween(0, 100000);
        $foodCharges = fake()->numberBetween(0, 50000);
        $subtotal = $consoleCharges + $foodCharges;
        $tax = $subtotal * 0.10;

        return [
            'invoice_number' => 'INV-' . fake()->unique()->numerify('######'),
            'rental_session_id' => fake()->optional(0.7)->randomElement(RentalSession::pluck('id')->toArray()),
            'order_id' => fake()->optional(0.5)->randomElement(Order::pluck('id')->toArray()),
            'user_id' => User::factory(),
            'customer_name' => fake()->optional(0.7)->name(),
            'console_charges' => $consoleCharges,
            'food_charges' => $foodCharges,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
            'payment_status' => fake()->randomElement(['unpaid', 'paid']),
            'payment_method' => fake()->optional(0.7)->randomElement(['cash', 'card', 'transfer']),
            'paid_at' => fake()->optional(0.7)->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
