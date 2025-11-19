<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\RentalSession;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    private const TAX_RATE = 0.10;

    public function createCombinedInvoice(RentalSession $session, ?Order $order = null): Invoice
    {
        return DB::transaction(function () use ($session, $order) {
            $consoleCharges = $session->total_cost;
            $foodCharges = $order ? $order->total : 0;

            $subtotal = $consoleCharges + $foodCharges;
            $tax = $subtotal * self::TAX_RATE;
            $total = $subtotal + $tax;

            return Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'rental_session_id' => $session->id,
                'order_id' => $order?->id,
                'user_id' => auth()->id(),
                'customer_name' => $session->customer_name ?? $order?->customer_name,
                'console_charges' => $consoleCharges,
                'food_charges' => $foodCharges,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);
        });
    }

    public function createConsoleInvoice(RentalSession $session): Invoice
    {
        $consoleCharges = $session->total_cost;
        $tax = $consoleCharges * self::TAX_RATE;
        $total = $consoleCharges + $tax;

        return Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'rental_session_id' => $session->id,
            'user_id' => auth()->id(),
            'customer_name' => $session->customer_name,
            'console_charges' => $consoleCharges,
            'food_charges' => 0,
            'subtotal' => $consoleCharges,
            'tax' => $tax,
            'total' => $total,
        ]);
    }

    public function createFoodInvoice(Order $order): Invoice
    {
        $foodCharges = $order->total;
        $tax = $order->tax;
        $total = $foodCharges;

        return Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'customer_name' => $order->customer_name,
            'console_charges' => 0,
            'food_charges' => $foodCharges,
            'subtotal' => $order->subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);
    }

    public function markAsPaid(Invoice $invoice, string $paymentMethod): Invoice
    {
        $invoice->update([
            'payment_status' => 'paid',
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
        ]);

        return $invoice;
    }

    private function generateInvoiceNumber(): string
    {
        return 'INV-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
