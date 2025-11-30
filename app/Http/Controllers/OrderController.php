<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\FoodItem;
use App\Models\RentalSession;
use App\Models\Customer;
use App\Services\OrderService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private InvoiceService $invoiceService
    ) {}

    public function index()
    {
        $orders = Order::with(['user', 'rentalSession', 'customer'])
            ->latest()
            ->paginate(20);

        return view('orders.index', compact('orders'));
    }

    public function create(Request $request)
    {
        $sessionId = $request->query('session_id');
        $session = $sessionId ? RentalSession::with('customer')->find($sessionId) : null;

        $foodItems = FoodItem::with('category')
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->get();

        $customers = Customer::where('is_active', true)->orderBy('name')->get();

        return view('orders.create', compact('foodItems', 'session', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rental_session_id' => 'nullable|exists:rental_sessions,id',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Get customer name from database or use walk-in name
        if ($request->customer_id) {
            $customer = Customer::find($request->customer_id);
            $validated['customer_name'] = $customer->name;
        }

        try {
            $order = $this->orderService->createOrder($validated);

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order created successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order->load(['items.foodItem', 'rentalSession', 'user', 'customer']);
        return view('orders.show', compact('order'));
    }

    public function markAsPaid(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:cash,card,transfer',
        ]);

        try {
            // If linked to rental session, update session invoice
            if ($order->rental_session_id) {
                $session = RentalSession::find($order->rental_session_id);
                if ($session && $session->status === 'completed') {
                    $invoice = $this->invoiceService->createSessionInvoice($session);
                    $this->invoiceService->markAsPaid($invoice, $validated['payment_method']);
                } else {
                    // Session not completed yet, just mark order as paid
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'completed',
                        'payment_method' => $validated['payment_method'],
                    ]);
                }
            } else {
                // Standalone order, create separate invoice
                $invoice = $this->invoiceService->createFoodOnlyInvoice($order);
                $this->invoiceService->markAsPaid($invoice, $validated['payment_method']);
            }

            return redirect()->route('orders.show', $order)
                ->with('success', 'Payment received successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function printReceipt(Order $order)
    {
        $order->load(['items.foodItem', 'user', 'customer', 'invoice']);

        // Filter only items that belong to food & beverage
        $foodOrders = collect([$order]);

        $pdf = Pdf::loadView('orders.thermal-receipt', [
            'order' => $order,
            'foodOrders' => $foodOrders,
        ])
            ->setPaper([0, 0, 226.77, 841.89]); // 80mm width thermal paper

        return $pdf->download('receipt-order-' . $order->order_number . '.pdf');
    }
}
