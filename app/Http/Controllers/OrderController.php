<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\FoodItem;
use App\Models\RentalSession;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
    }

    public function index()
    {
        $orders = Order::with(['user', 'rentalSession'])
            ->latest()
            ->paginate(20);

        return view('orders.index', compact('orders'));
    }

    public function create(Request $request)
    {
        $sessionId = $request->query('session_id');
        $session = $sessionId ? RentalSession::find($sessionId) : null;

        $foodItems = FoodItem::with('category')
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->get();

        return view('orders.create', compact('foodItems', 'session'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rental_session_id' => 'nullable|exists:rental_sessions,id',
            'customer_name' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

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
        $order->load(['items.foodItem', 'rentalSession', 'user']);
        return view('orders.show', compact('order'));
    }

    public function markAsPaid(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:cash,card,transfer',
        ]);

        $order->update([
            'payment_status' => 'paid',
            'status' => 'completed',
            'payment_method' => $validated['payment_method'],
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order marked as paid successfully');
    }
}
