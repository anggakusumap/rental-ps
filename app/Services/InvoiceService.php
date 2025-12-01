<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\RentalSession;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Create combined invoice (console + food orders linked to session)
     */
    public function createCombinedInvoice(RentalSession $session, ?Order $order = null): Invoice
    {
        return DB::transaction(function () use ($session, $order) {
            // Check if invoice already exists
            if ($session->invoice) {
                return $session->invoice;
            }

            $consoleCharges = $session->total_cost;

            // Get all food orders linked to this session
            $foodOrders = Order::where('rental_session_id', $session->id)->get();
            $foodCharges = $foodOrders->sum('total');

            $subtotal = $consoleCharges + $foodCharges;
            $tax = 0; // No tax
            $total = $subtotal;

            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'rental_session_id' => $session->id,
                'order_id' => null,
                'user_id' => auth()->id(),
                'customer_name' => $session->customer_name,
                'console_charges' => $consoleCharges,
                'food_charges' => $foodCharges,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'payment_status' => 'unpaid',
            ]);

            return $invoice;
        });
    }

    /**
     * Create invoice for console only
     */
    public function createConsoleInvoice(RentalSession $session): Invoice
    {
        return DB::transaction(function () use ($session) {
            if ($session->invoice) {
                return $session->invoice;
            }

            $consoleCharges = $session->total_cost;
            $subtotal = $consoleCharges;
            $tax = 0; // No tax
            $total = $subtotal;

            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'rental_session_id' => $session->id,
                'order_id' => null,
                'user_id' => auth()->id(),
                'customer_name' => $session->customer_name,
                'console_charges' => $consoleCharges,
                'food_charges' => 0,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'payment_status' => 'unpaid',
            ]);

            return $invoice;
        });
    }

    /**
     * Create invoice for food only
     */
    public function createFoodInvoice(Order $order): Invoice
    {
        return DB::transaction(function () use ($order) {
            $existingInvoice = Invoice::where('order_id', $order->id)->first();
            if ($existingInvoice) {
                return $existingInvoice;
            }

            $foodCharges = $order->total;
            $subtotal = $order->subtotal;
            $tax = 0; // No tax
            $total = $subtotal;

            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'rental_session_id' => $order->rental_session_id,
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'customer_name' => $order->customer_name,
                'console_charges' => 0,
                'food_charges' => $foodCharges,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'payment_status' => 'unpaid',
            ]);

            return $invoice;
        });
    }

    public function createSessionInvoice(RentalSession $session): Invoice
    {
        return $this->createCombinedInvoice($session);
    }

    public function createFoodOnlyInvoice(Order $order): Invoice
    {
        return $this->createFoodInvoice($order);
    }

    public function markAsPaid(Invoice $invoice, string $paymentMethod): Invoice
    {
        return DB::transaction(function () use ($invoice, $paymentMethod) {
            $invoice->update([
                'payment_status' => 'paid',
                'payment_method' => $paymentMethod,
                'paid_at' => now(),
            ]);

            if ($invoice->rentalSession) {
                $invoice->rentalSession->update([
                    'payment_status' => 'paid',
                    'payment_method' => $paymentMethod,
                    'paid_at' => now(),
                ]);
            }

            if ($invoice->rentalSession) {
                Order::where('rental_session_id', $invoice->rentalSession->id)
                    ->update([
                        'payment_status' => 'paid',
                        'payment_method' => $paymentMethod,
                        'status' => 'completed',
                    ]);
            }

            if ($invoice->order) {
                $invoice->order->update([
                    'payment_status' => 'paid',
                    'payment_method' => $paymentMethod,
                    'status' => 'completed',
                ]);
            }

            return $invoice->fresh();
        });
    }

    private function generateInvoiceNumber(): string
    {
        return 'INV-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
