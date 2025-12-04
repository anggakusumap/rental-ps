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

    public function index(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        $invoices = Invoice::with(['rentalSession', 'order', 'user'])
            ->whereDate('created_at', $date)
            ->latest()
            ->paginate(20);

        // Get all invoices for the date (not paginated) for stats calculation
        $allInvoices = Invoice::whereDate('created_at', $date)->get();

        // Calculate stats
        $stats = [
            // Console Only: has console charges but no food charges
            'consoleOnly' => $allInvoices->where('console_charges', '>', 0)
                ->where('food_charges', '=', 0)
                ->count(),
            'consoleOnlyRevenue' => $allInvoices->where('console_charges', '>', 0)
                ->where('food_charges', '=', 0)
                ->sum('total'),

            // F&B Only: has food charges but no console charges
            'foodOnly' => $allInvoices->where('food_charges', '>', 0)
                ->where('console_charges', '=', 0)
                ->count(),
            'foodOnlyRevenue' => $allInvoices->where('food_charges', '>', 0)
                ->where('console_charges', '=', 0)
                ->sum('total'),

            // Combined: has both console and food charges
            'combined' => $allInvoices->where('console_charges', '>', 0)
                ->where('food_charges', '>', 0)
                ->count(),
            'combinedRevenue' => $allInvoices->where('console_charges', '>', 0)
                ->where('food_charges', '>', 0)
                ->sum('total'),

            // Totals
            'totalInvoices' => $allInvoices->count(),
            'totalRevenue' => $allInvoices->sum('total'),
        ];

        return view('invoices.index', compact('invoices', 'date', 'stats'));
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
            $session = isset($validated['rental_session_id']) && $validated['rental_session_id']
                ? RentalSession::find($validated['rental_session_id'])
                : null;
            $order = isset($validated['order_id']) && $validated['order_id']
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
