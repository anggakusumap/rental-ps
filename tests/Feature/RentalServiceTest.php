<?php

namespace Tests\Unit;

use App\Models\Console;
use App\Models\ConsoleType;
use App\Models\RentalSession;
use App\Models\Package;
use App\Services\RentalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculate_cost_with_hourly_rate()
    {
        $consoleType = ConsoleType::factory()->create(['hourly_rate' => 20000]);
        $console = Console::factory()->create(['console_type_id' => $consoleType->id]);

        $session = RentalSession::factory()->create([
            'console_id' => $console->id,
            'package_id' => null,
            'start_time' => now()->subMinutes(90),
            'total_paused_minutes' => 0,
            'status' => 'active',
        ]);

        $service = new RentalService();
        $cost = $service->calculateCurrentCost($session);

        // 90 minutes = 1.5 hours * 20000 = 30000
        $this->assertEquals(30000, $cost);
    }

    public function test_calculate_cost_with_package()
    {
        $consoleType = ConsoleType::factory()->create(['hourly_rate' => 20000]);
        $console = Console::factory()->create(['console_type_id' => $consoleType->id]);
        $package = Package::factory()->create([
            'duration_minutes' => 60,
            'price' => 15000,
        ]);

        $session = RentalSession::factory()->create([
            'console_id' => $console->id,
            'package_id' => $package->id,
            'start_time' => now()->subMinutes(45),
            'total_paused_minutes' => 0,
            'status' => 'active',
        ]);

        $service = new RentalService();
        $cost = $service->calculateCurrentCost($session);

        // Within package duration, should be package price
        $this->assertEquals(15000, $cost);
    }

    public function test_calculate_cost_with_package_overtime()
    {
        $consoleType = ConsoleType::factory()->create(['hourly_rate' => 20000]);
        $console = Console::factory()->create(['console_type_id' => $consoleType->id]);
        $package = Package::factory()->create([
            'duration_minutes' => 60,
            'price' => 15000,
        ]);

        $session = RentalSession::factory()->create([
            'console_id' => $console->id,
            'package_id' => $package->id,
            'start_time' => now()->subMinutes(90),
            'total_paused_minutes' => 0,
            'status' => 'active',
        ]);

        $service = new RentalService();
        $cost = $service->calculateCurrentCost($session);

        // Package price + 30 minutes overtime (0.5 * 20000 = 10000)
        $this->assertEquals(25000, $cost);
    }
}
