<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $order->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            width: 80mm;
            padding: 4mm;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }

        .line {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .double-line {
            border-top: 2px solid #000;
            margin: 6px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 2px;
        }
        .header p {
            font-size: 10px;
            margin: 1px 0;
        }

        .section-title {
            margin-top: 6px;
            margin-bottom: 4px;
            font-weight: bold;
            font-size: 12px;
        }

        .item-row {
            margin: 2px 0;
        }
        .qty {
            display: inline-block;
            width: 24px;
            text-align: left;
        }
        .price-note {
            font-size: 9px;
            color: #666;
            margin-left: 32px;
        }

        .grand-total {
            font-size: 14px;
            font-weight: bold;
        }

        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>

<!-- Store Header -->
<div class="header center">
    <h1>PS RENTAL</h1>
    <p>Gaming Center</p>
    <p>Jl. Contoh No. 123</p>
    <p>Tel: (0361) 123456</p>
</div>

<div class="double-line"></div>

<!-- Session Info -->
<div class="center">
    <p class="bold">SESSION #{{ $order->id }}</p>
    <p>{{ now()->format('d M Y, H:i:s') }}</p>
</div>

<div class="line"></div>

<!-- Customer & Staff -->
<p>Customer: {{ $order->customer_name ?? 'Walk-in' }}</p>
<p>Cashier: {{ $order->user->name }}</p>

<div class="double-line"></div>

<!-- FOOD & BEVERAGE -->
@php $foodTotal = 0; @endphp

@if($foodOrders->count() > 0)
    <p class="section-title">FOOD & BEVERAGE</p>

    @foreach($foodOrders as $foodOrder)
        @foreach($foodOrder->items as $item)

            <div class="item-row">
                <div class="row">
                        <span>
                            <span class="qty">{{ $item->quantity }}x</span>
                            {{ $item->foodItem->name }}
                        </span>
                    <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="price-note">
                    @ Rp {{ number_format($item->price, 0, ',', '.') }}
                </div>
            </div>

            @php $foodTotal += $item->subtotal; @endphp

        @endforeach
    @endforeach

    <div class="line"></div>

    <div class="row bold">
        <span>F&B Total:</span>
        <span>Rp {{ number_format($foodTotal, 0, ',', '.') }}</span>
    </div>
@endif

<!-- Totals -->
<div class="double-line"></div>

@php
    $subtotal = $order->total_cost + $foodTotal;
    $tax = $subtotal * 0.10;
    $grandTotal = $subtotal + $tax;
@endphp

<div class="row">
    <span>Subtotal:</span>
    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
</div>

<div class="row">
    <span>Tax (10%):</span>
    <span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
</div>

<div class="double-line"></div>

<div class="row grand-total">
    <span>TOTAL:</span>
    <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
</div>

<!-- Payment Status -->
<div class="double-line"></div>

@if($order->invoice && $order->invoice->payment_status === 'paid')
    <div class="center bold">
        PAID - {{ strtoupper($order->invoice->payment_method) }}
    </div>
    <div class="center" style="font-size: 10px;">
        {{ $order->invoice->paid_at->format('d M Y, H:i') }}
    </div>
@else
    <div class="center bold">PAYMENT PENDING</div>
@endif

<!-- Footer -->
<div class="double-line"></div>

<div class="footer center" style="font-size: 10px;">
    <p>Thank you for your visit!</p>
    <p>Please come again</p>
    <p style="margin-top: 6px;">www.psrental.com</p>
</div>

</body>
</html>
