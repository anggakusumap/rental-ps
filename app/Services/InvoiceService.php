<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\RentalSession;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    private const TAX_RATE = 0.10;

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
            $tax = $subtotal * self::TAX_RATE;
            $total = $subtotal + $tax;

            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'rental_session_id' => $session->id,
                'order_id' => null, // Combined invoices link to session, not individual order
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
            // Check if invoice already exists
            if ($session->invoice) {
                return $session->invoice;
            }

            $consoleCharges = $session->total_cost;
            $subtotal = $consoleCharges;
            $tax = $subtotal * self::TAX_RATE;
            $total = $subtotal + $tax;

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
     * Create invoice for food only (standalone order or specific order)
     */
    public function createFoodInvoice(Order $order): Invoice
    {
        return DB::transaction(function () use ($order) {
            // Check if invoice already exists for this order
            $existingInvoice = Invoice::where('order_id', $order->id)->first();
            if ($existingInvoice) {
                return $existingInvoice;
            }

            $foodCharges = $order->total;
            $subtotal = $order->subtotal;
            $tax = $order->tax;
            $total = $foodCharges;

            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'rental_session_id' => $order->rental_session_id, // Can be null for standalone orders
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

    /**
     * Create invoice for a rental session (includes all linked food orders)
     * Alias for createCombinedInvoice for backward compatibility
     */
    public function createSessionInvoice(RentalSession $session): Invoice
    {
        return $this->createCombinedInvoice($session);
    }

    /**
     * Create invoice for standalone food order (not linked to session)
     * Alias for createFoodInvoice for backward compatibility
     */
    public function createFoodOnlyInvoice(Order $order): Invoice
    {
        return $this->createFoodInvoice($order);
    }

    /**
     * Mark invoice as paid and update related records
     */
    public function markAsPaid(Invoice $invoice, string $paymentMethod): Invoice
    {
        return DB::transaction(function () use ($invoice, $paymentMethod) {
            $invoice->update([
                'payment_status' => 'paid',
                'payment_method' => $paymentMethod,
                'paid_at' => now(),
            ]);

            // Update rental session if exists
            if ($invoice->rentalSession) {
                $invoice->rentalSession->update([
                    'payment_status' => 'paid',
                    'payment_method' => $paymentMethod,
                    'paid_at' => now(),
                ]);
            }

            // Update all linked orders (for combined invoices)
            if ($invoice->rentalSession) {
                Order::where('rental_session_id', $invoice->rentalSession->id)
                    ->update([
                        'payment_status' => 'paid',
                        'payment_method' => $paymentMethod,
                        'status' => 'completed',
                    ]);
            }

            // Update standalone order if exists
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

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber(): string
    {
        return 'INV-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
