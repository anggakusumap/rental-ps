<?php

namespace Database\Seeders;

use App\Models\Console;
use App\Models\ConsoleType;
use Illuminate\Database\Seeder;

class ConsoleSeeder extends Seeder
{
    public function run(): void
    {
        $consoleTypes = ConsoleType::all();

        $consoleCounter = 1; // Global counter for unique console numbers

        foreach ($consoleTypes as $type) {
            // Get first 3 letters of console type name
            $prefix = strtoupper(substr(str_replace(' ', '', $type->name), 0, 3));

            for ($i = 1; $i <= 5; $i++) {
                Console::create([
                    'console_type_id' => $type->id,
                    'console_number' => $prefix . '-' . str_pad($consoleCounter, 3, '0', STR_PAD_LEFT),
                    'status' => 'available',
                ]);

                $consoleCounter++; // Increment global counter
            }
        }
    }
}
