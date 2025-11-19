<?php

namespace App\Services;

use App\Models\Console;
use App\Models\RentalSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RentalService
{
    public function startSession(array $data): RentalSession
    {
        return DB::transaction(function () use ($data) {
            $console = Console::findOrFail($data['console_id']);

            if ($console->status !== 'available') {
                throw new \Exception('Console is not available');
            }

            $session = RentalSession::create([
                'console_id' => $data['console_id'],
                'user_id' => auth()->id(),
                'package_id' => $data['package_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'start_time' => now(),
                'status' => 'active',
                'notes' => $data['notes'] ?? null,
            ]);

            $console->update(['status' => 'occupied']);

            return $session;
        });
    }

    public function pauseSession(RentalSession $session): RentalSession
    {
        if ($session->status !== 'active') {
            throw new \Exception('Session is not active');
        }

        $session->update([
            'status' => 'paused',
            'paused_at' => now(),
        ]);

        return $session;
    }

    public function resumeSession(RentalSession $session): RentalSession
    {
        if ($session->status !== 'paused') {
            throw new \Exception('Session is not paused');
        }

        $pausedMinutes = now()->diffInMinutes($session->paused_at);

        $session->update([
            'status' => 'active',
            'total_paused_minutes' => $session->total_paused_minutes + $pausedMinutes,
            'paused_at' => null,
        ]);

        return $session;
    }

    public function extendSession(RentalSession $session, int $additionalMinutes): RentalSession
    {
        // Extension just noted, actual cost calculated at end
        $session->update([
            'notes' => ($session->notes ?? '') . "\nExtended by {$additionalMinutes} minutes at " . now()->format('H:i'),
        ]);

        return $session;
    }

    public function endSession(RentalSession $session): RentalSession
    {
        return DB::transaction(function () use ($session) {
            if (!in_array($session->status, ['active', 'paused'])) {
                throw new \Exception('Session is not active or paused');
            }

            // Calculate total active minutes
            $endTime = now();
            $totalMinutes = $endTime->diffInMinutes($session->start_time);
            $activeMinutes = $totalMinutes - $session->total_paused_minutes;

            // Calculate cost
            $cost = $this->calculateCost($session, $activeMinutes);

            $session->update([
                'end_time' => $endTime,
                'status' => 'completed',
                'total_cost' => $cost,
            ]);

            $session->console->update(['status' => 'available']);

            return $session;
        });
    }

    public function calculateCurrentCost(RentalSession $session): float
    {
        if ($session->status === 'paused') {
            $totalMinutes = $session->paused_at->diffInMinutes($session->start_time);
        } else {
            $totalMinutes = now()->diffInMinutes($session->start_time);
        }

        $activeMinutes = $totalMinutes - $session->total_paused_minutes;

        return $this->calculateCost($session, $activeMinutes);
    }

    private function calculateCost(RentalSession $session, int $activeMinutes): float
    {
        // If package was used
        if ($session->package) {
            $packageMinutes = $session->package->duration_minutes;
            $packagePrice = $session->package->price;

            if ($activeMinutes <= $packageMinutes) {
                return (float) $packagePrice;
            }

            // Overtime charges at hourly rate
            $overtimeMinutes = $activeMinutes - $packageMinutes;
            $hourlyRate = $session->console->consoleType->hourly_rate;
            $overtimeCost = ($overtimeMinutes / 60) * $hourlyRate;

            return (float) ($packagePrice + $overtimeCost);
        }

        // Hourly rate calculation
        $hourlyRate = $session->console->consoleType->hourly_rate;
        return (float) (($activeMinutes / 60) * $hourlyRate);
    }
}
