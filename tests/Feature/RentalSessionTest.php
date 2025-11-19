<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Console;
use App\Models\ConsoleType;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalSessionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->user);
    }

    public function test_can_start_rental_session()
    {
        $consoleType = ConsoleType::factory()->create(['hourly_rate' => 20000]);
        $console = Console::factory()->create(['console_type_id' => $consoleType->id]);
        $package = Package::factory()->create();

        $response = $this->post(route('rental-sessions.store'), [
            'console_id' => $console->id,
            'package_id' => $package->id,
            'customer_name' => 'John Doe',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('rental_sessions', [
            'console_id' => $console->id,
            'customer_name' => 'John Doe',
            'status' => 'active',
        ]);

        $console->refresh();
        $this->assertEquals('occupied', $console->status);
    }

    public function test_can_pause_and_resume_session()
    {
        $session = \App\Models\RentalSession::factory()->create(['status' => 'active']);

        // Pause
        $response = $this->post(route('rental-sessions.pause', $session));
        $session->refresh();
        $this->assertEquals('paused', $session->status);
        $this->assertNotNull($session->paused_at);

        // Resume
        $response = $this->post(route('rental-sessions.resume', $session));
        $session->refresh();
        $this->assertEquals('active', $session->status);
        $this->assertNull($session->paused_at);
        $this->assertGreaterThan(0, $session->total_paused_minutes);
    }

    public function test_can_end_session()
    {
        $session = \App\Models\RentalSession::factory()->create(['status' => 'active']);

        $response = $this->post(route('rental-sessions.end', $session));

        $session->refresh();
        $this->assertEquals('completed', $session->status);
        $this->assertNotNull($session->end_time);
        $this->assertGreaterThan(0, $session->total_cost);

        $console = $session->console;
        $this->assertEquals('available', $console->status);
    }
}
