<?php

namespace App\Http\Controllers;

use App\Models\Console;
use App\Models\RentalSession;
use App\Models\Invoice;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $availableConsoles = Console::where('status', 'available')->count();
        $occupiedConsoles = Console::where('status', 'occupied')->count();
        $activeSessions = RentalSession::whereIn('status', ['active', 'paused'])->count();

        $todayRevenue = Invoice::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total');

        $recentSessions = RentalSession::with(['console.consoleType', 'user'])
            ->whereIn('status', ['active', 'paused'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'availableConsoles',
            'occupiedConsoles',
            'activeSessions',
            'todayRevenue',
            'recentSessions'
        ));
    }
}
