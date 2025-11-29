<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\GenerateReportCommand::class,
        Commands\EndExpiredSessionsCommand::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Generate daily report at midnight
        $schedule->command('report:daily')->dailyAt('00:01');

        // End sessions that have been active for more than 12 hours
        $schedule->command('sessions:end-expired 12')->hourly();

        // Backup database daily
        $schedule->command('backup:run')->daily();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
