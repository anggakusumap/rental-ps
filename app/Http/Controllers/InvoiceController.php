<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\RentalSession;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService)
    {
    }

    public function index()
    {
        $invoices = Invoice::with(['rentalSession', 'order', 'user'])
            ->latest()
            ->paginate(20);

        return view('invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $sessionId = $request->query('session_id');
        $orderId = $request->query('order_id');

        $session = $sessionId ? RentalSession::with('console.consoleType')->find($sessionId) : null;
        $order = $orderId ? Order::with('items.foodItem')->find($orderId) : null;

        return view('invoices.create', compact('session', 'order'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rental_session_id' => 'nullable|exists:rental_sessions,id',
            'order_id' => 'nullable|exists:orders,id',
            'invoice_type' => 'required|in:combined,console_only,food_only',
        ]);

        try {
            $session = $validated['rental_session_id']
                ? RentalSession::find($validated['rental_session_id'])
                : null;
            $order = $validated['order_id']
                ? Order::find($validated['order_id'])
                : null;

            $invoice = match($validated['invoice_type']) {
                'combined' => $this->invoiceService->createCombinedInvoice($session, $order),
                'console_only' => $this->invoiceService->createConsoleInvoice($session),
                'food_only' => $this->invoiceService->createFoodInvoice($order),
            };

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice created successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['rentalSession.console.consoleType', 'order.items.foodItem', 'user']);
        return view('invoices.show', compact('invoice'));
    }

    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:cash,card,transfer',
        ]);

        $this->invoiceService->markAsPaid($invoice, $validated['payment_method']);

        return back()->with('success', 'Invoice marked as paid');
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['rentalSession.console.consoleType', 'order.items.foodItem', 'user']);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }
}
