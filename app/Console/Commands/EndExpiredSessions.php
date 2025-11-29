<?php

namespace App\Console\Commands;

use App\Models\RentalSession;
use App\Services\RentalService;
use Illuminate\Console\Command;

class EndExpiredSessionsCommand extends Command
{
    protected $signature = 'sessions:end-expired {hours=12}';
    protected $description = 'Automatically end sessions that have been active for too long';

    public function __construct(private RentalService $rentalService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $hours = $this->argument('hours');
        $cutoffTime = now()->subHours($hours);

        $sessions = RentalSession::where('status', 'active')
            ->where('start_time', '<', $cutoffTime)
            ->get();

        $this->info("Found {$sessions->count()} expired sessions");

        foreach ($sessions as $session) {
            try {
                $this->rentalService->endSession($session);
                $this->line("✓ Ended session #{$session->id}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to end session #{$session->id}: {$e->getMessage()}");
            }
        }

        return Command::SUCCESS;
    }
}
