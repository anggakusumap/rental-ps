<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $rentalSession->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            width: 58mm;
            padding: 2mm;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .line {
            border-top: 1px dashed #000;
            margin: 3px 0;
        }
        .double-line {
            border-top: 2px solid #000;
            margin: 4px 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin: 1px 0;
        }
        .header h1 {
            font-size: 14px;
            margin-bottom: 2px;
        }
        .header p {
            font-size: 9px;
            margin: 0;
        }
        .section-title {
            margin-top: 4px;
            margin-bottom: 2px;
            font-weight: bold;
            font-size: 11px;
        }
        .item-row {
            margin: 2px 0;
        }
        .qty {
            display: inline-block;
            width: 20px;
            text-align: left;
        }
        .price-note {
            font-size: 8px;
            color: #666;
            margin-left: 24px;
        }
        .grand-total {
            font-size: 12px;
            font-weight: bold;
        }
        .footer {
            font-size: 9px;
            margin-top: 6px;
        }
        @media print {
            @page {
                size: 58mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>

<!-- Store Header -->
<div class="header center">
    <h1>The Room PlayStation</h1>
    <p>Gaming Center</p>
    <p>Jl. Contoh No. 123</p>
    <p>Tel: (0361) 123456</p>
</div>

<div class="double-line"></div>

<!-- Session Info -->
<div class="center">
    <p class="bold">SESSION #{{ $rentalSession->id }}</p>
    <p style="font-size: 9px;">{{ now()->format('d M Y, H:i') }}</p>
</div>

<div class="line"></div>

<!-- Customer & Staff -->
<p style="font-size: 9px;">Customer: {{ $rentalSession->customer_name ?? 'Walk-in' }}</p>
<p style="font-size: 9px;">Cashier: {{ $rentalSession->user->name }}</p>

<div class="double-line"></div>

<!-- Console Rental -->
<p class="section-title">CONSOLE RENTAL</p>
<p class="bold" style="font-size: 10px;">{{ $rentalSession->console->console_number }}</p>
<p style="font-size: 9px;">{{ $rentalSession->console->consoleType->name }}</p>
<div class="row" style="font-size: 9px;">
    <span>{{ $rentalSession->start_time->format('H:i') }} - {{ $rentalSession->end_time?->format('H:i') }}</span>
    <span>
        @php
            $duration = $rentalSession->end_time
                ? $rentalSession->end_time->diffInMinutes($rentalSession->start_time) - $rentalSession->total_paused_minutes
                : 0;
        @endphp
        {{ $duration }}m
    </span>
</div>
@if($rentalSession->package)
    <p style="font-size: 8px; color: #666;">Package: {{ $rentalSession->package->name }}</p>
@endif

<div class="line"></div>

<div class="row bold">
    <span>Console:</span>
    <span>{{ number_format($rentalSession->total_cost, 0, ',', '.') }}</span>
</div>

<!-- FOOD & BEVERAGE -->
@php $foodTotal = 0; @endphp

@if($foodOrders->count() > 0)
    <div class="double-line"></div>
    <p class="section-title">FOOD & BEVERAGE</p>

    @foreach($foodOrders as $foodOrder)
        @foreach($foodOrder->items as $item)
            <div class="item-row">
                <div class="row" style="font-size: 9px;">
                    <span>
                        <span class="qty">{{ $item->quantity }}x</span>
                        {{ $item->foodItem->name }}
                    </span>
                    <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="price-note">
                    @ {{ number_format($item->price, 0, ',', '.') }}
                </div>
            </div>
            @php $foodTotal += $item->subtotal; @endphp
        @endforeach
    @endforeach

    <div class="line"></div>

    <div class="row bold">
        <span>F&B:</span>
        <span>{{ number_format($foodTotal, 0, ',', '.') }}</span>
    </div>
@endif

<!-- Totals -->
<div class="double-line"></div>

@php
    $grandTotal = $rentalSession->total_cost + $foodTotal;
@endphp

<div class="row grand-total">
    <span>TOTAL:</span>
    <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
</div>

<!-- Payment Status -->
<div class="double-line"></div>

@if($rentalSession->payment_status === 'paid')
    <div class="center bold">
        PAID - {{ strtoupper($rentalSession->payment_method) }}
    </div>
    <div class="center" style="font-size: 8px;">
        {{ $rentalSession->paid_at->format('d M Y, H:i') }}
    </div>
@else
    <div class="center bold">PAYMENT PENDING</div>
@endif

<!-- Footer -->
<div class="double-line"></div>

<div class="footer center">
    <p>Thank you for your visit!</p>
    <p>Please come again</p>
</div>

</body>
</html>
