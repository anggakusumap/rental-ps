<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class GenerateReportCommand extends Command
{
    protected $signature = 'report:daily {date?}';
    protected $description = 'Generate daily revenue report';

    public function handle()
    {
        $date = $this->argument('date') ?? now()->toDateString();

        $invoices = Invoice::whereDate('created_at', $date)
            ->where('payment_status', 'paid')
            ->get();

        $totalRevenue = $invoices->sum('total');
        $consoleRevenue = $invoices->sum('console_charges');
        $foodRevenue = $invoices->sum('food_charges');

        $this->info("Daily Report for {$date}");
        $this->info("========================");
        $this->line("Total Revenue: Rp " . number_format($totalRevenue, 0, ',', '.'));
        $this->line("Console Revenue: Rp " . number_format($consoleRevenue, 0, ',', '.'));
        $this->line("Food Revenue: Rp " . number_format($foodRevenue, 0, ',', '.'));
        $this->line("Number of Invoices: " . $invoices->count());

        return Command::SUCCESS;
    }
}
