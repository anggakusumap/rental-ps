<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\RentalSession;
use App\Models\Console;
use App\Models\Package;
use App\Services\RentalService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class RentalSessionController extends Controller
{
    public function __construct(
        private RentalService $rentalService,
        private InvoiceService $invoiceService
    ) {
    }

    public function index(Request $request)
    {
        // Default date = today
        $date = $request->input('date', now()->toDateString());

        $sessions = RentalSession::with(['console.consoleType', 'package', 'user'])
            ->whereDate('created_at', $date)
            ->latest()
            ->paginate(20);

        return view('rental-sessions.index', compact('sessions', 'date'));
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
            'customer_id' => 'nullable|exists:customers,id',
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

            return redirect()->route('rental-sessions.show', $session)
                ->with('success', 'Session ended successfully. You can now process payment.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mark session as paid (creates invoice and marks it as paid)
     */
    public function markAsPaid(Request $request, RentalSession $rentalSession)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:cash,card,transfer',
        ]);

        try {
            // Check if session is completed
            if ($rentalSession->status !== 'completed') {
                return back()->with('error', 'Cannot process payment for active session. Please end the session first.');
            }

            // Create or get existing invoice
            $invoice = $rentalSession->invoice ?? $this->invoiceService->createSessionInvoice($rentalSession);

            // Mark as paid
            $this->invoiceService->markAsPaid($invoice, $validated['payment_method']);

            return redirect()->route('rental-sessions.show', $rentalSession)
                ->with('success', 'Payment received successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error processing payment: ' . $e->getMessage());
        }
    }

    /**
     * Print thermal receipt for completed session
     */
    public function printReceipt(RentalSession $rentalSession)
    {
        if ($rentalSession->status !== 'completed') {
            return back()->with('error', 'Cannot print receipt for active session');
        }

        $rentalSession->load(['console.consoleType', 'package', 'user', 'invoice']);
        $foodOrders = \App\Models\Order::where('rental_session_id', $rentalSession->id)
            ->with('items.foodItem')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('rental-sessions.thermal-receipt', compact('rentalSession', 'foodOrders'))
            ->setPaper([0, 0, 226.77, 841.89]); // 80mm width thermal paper

        return $pdf->download('receipt-session-' . $rentalSession->id . '.pdf');
    }
}
