<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private const TAX_RATE = 0.10; // 10% tax

    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Determine customer information
            $customerData = [
                'customer_id' => null,
                'customer_name' => null,
            ];

            // If customer_id is provided, get customer name from database
            if (!empty($data['customer_id'])) {
                $customer = Customer::find($data['customer_id']);
                if ($customer) {
                    $customerData['customer_id'] = $customer->id;
                    $customerData['customer_name'] = $customer->name;
                }
            } else {
                // Use provided walk-in customer name
                $customerData['customer_name'] = $data['customer_name'] ?? null;
            }

            $order = Order::create([
                'rental_session_id' => $data['rental_session_id'] ?? null,
                'user_id' => auth()->id(),
                'customer_id' => $customerData['customer_id'],
                'order_number' => $this->generateOrderNumber(),
                'customer_name' => $customerData['customer_name'],
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            $subtotal = 0;

            foreach ($data['items'] as $item) {
                $foodItem = \App\Models\FoodItem::findOrFail($item['food_item_id']);

                if ($foodItem->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$foodItem->name}");
                }

                $itemSubtotal = $foodItem->price * $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'food_item_id' => $item['food_item_id'],
                    'quantity' => $item['quantity'],
                    'price' => $foodItem->price,
                    'subtotal' => $itemSubtotal,
                ]);

                $foodItem->decreaseStock($item['quantity']);
                $subtotal += $itemSubtotal;
            }

            $tax = $subtotal * self::TAX_RATE;
            $total = $subtotal + $tax;

            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);

            return $order->fresh();
        });
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
