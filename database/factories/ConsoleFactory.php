<?php

namespace Database\Factories;

use App\Models\ConsoleType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsoleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'console_type_id' => ConsoleType::factory(),
            'console_number' => strtoupper(fake()->bothify('??-###')),
            'status' => 'available',
        ];
    }
}
