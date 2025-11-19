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

        foreach ($consoleTypes as $type) {
            for ($i = 1; $i <= 5; $i++) {
                Console::create([
                    'console_type_id' => $type->id,
                    'console_number' => strtoupper(substr($type->name, 0, 3)) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'status' => 'available',
                ]);
            }
        }
    }
}
