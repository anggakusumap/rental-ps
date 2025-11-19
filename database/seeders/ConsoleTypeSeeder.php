<?php

namespace Database\Seeders;

use App\Models\ConsoleType;
use Illuminate\Database\Seeder;

class ConsoleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'PlayStation 3', 'hourly_rate' => 15000, 'description' => 'Classic PS3 gaming experience'],
            ['name' => 'PlayStation 4', 'hourly_rate' => 20000, 'description' => 'Enhanced graphics and gameplay'],
            ['name' => 'PlayStation 5', 'hourly_rate' => 30000, 'description' => 'Latest generation console with ray tracing'],
            ['name' => 'PlayStation VR', 'hourly_rate' => 35000, 'description' => 'Immersive virtual reality gaming'],
        ];

        foreach ($types as $type) {
            ConsoleType::create($type);
        }
    }
}
