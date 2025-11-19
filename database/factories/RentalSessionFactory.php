<?php

namespace Database\Factories;

use App\Models\Console;
use App\Models\User;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

class RentalSessionFactory extends Factory
{
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('-2 hours', 'now');

        return [
            'console_id' => Console::factory(),
            'user_id' => User::factory(),
            'package_id' => fake()->optional(0.5)->randomElement(Package::pluck('id')->toArray()),
            'customer_name' => fake()->optional(0.7)->name(),
            'start_time' => $startTime,
            'end_time' => null,
            'paused_at' => null,
            'total_paused_minutes' => 0,
            'status' => 'active',
            'total_cost' => 0,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $startTime = $attributes['start_time'];
            $endTime = fake()->dateTimeBetween($startTime, '+3 hours');

            return [
                'end_time' => $endTime,
                'status' => 'completed',
                'total_cost' => fake()->numberBetween(15000, 100000),
            ];
        });
    }

    public function paused(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'paused',
                'paused_at' => now(),
                'total_paused_minutes' => fake()->numberBetween(5, 30),
            ];
        });
    }
}
