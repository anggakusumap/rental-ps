<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\RentalSession;
use App\Models\Console;
use App\Models\Package;
use App\Services\RentalService;
use Illuminate\Http\Request;

class RentalSessionController extends Controller
{
    public function __construct(private RentalService $rentalService)
    {
    }

    public function index()
    {
        $sessions = RentalSession::with(['console.consoleType', 'package', 'user'])
            ->latest()
            ->paginate(20);

        return view('rental-sessions.index', compact('sessions'));
    }

    public function create()
    {
        $consoles = Console::with('consoleType')
            ->where('status', 'available')
            ->get();
        $packages = Package::where('is_active', true)->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();

        return view('rental-sessions.create', compact('consoles', 'packages', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'console_id' => 'required|exists:consoles,id',
            'package_id' => 'nullable|exists:packages,id',
            'customer_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            $session = $this->rentalService->startSession($validated);

            return redirect()->route('rental-sessions.show', $session)
                ->with('success', 'Rental session started successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(RentalSession $rentalSession)
    {
        $rentalSession->load(['console.consoleType', 'package', 'user']);

        $currentCost = null;
        if (in_array($rentalSession->status, ['active', 'paused'])) {
            $currentCost = $this->rentalService->calculateCurrentCost($rentalSession);
        }

        return view('rental-sessions.show', compact('rentalSession', 'currentCost'));
    }

    public function pause(RentalSession $rentalSession)
    {
        try {
            $this->rentalService->pauseSession($rentalSession);
            return back()->with('success', 'Session paused successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function resume(RentalSession $rentalSession)
    {
        try {
            $this->rentalService->resumeSession($rentalSession);
            return back()->with('success', 'Session resumed successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function extend(Request $request, RentalSession $rentalSession)
    {
        $validated = $request->validate([
            'additional_minutes' => 'required|integer|min:1',
        ]);

        try {
            $this->rentalService->extendSession($rentalSession, $validated['additional_minutes']);
            return back()->with('success', 'Session extended successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function end(RentalSession $rentalSession)
    {
        try {
            $session = $this->rentalService->endSession($rentalSession);

            return redirect()->route('invoices.create', ['session_id' => $session->id])
                ->with('success', 'Session ended successfully. Create invoice now.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
