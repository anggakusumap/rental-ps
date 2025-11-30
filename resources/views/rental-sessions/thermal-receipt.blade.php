{{-- resources/views/rental-sessions/thermal-receipt.blade.php --}}
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
            font-size: 11px;
            width: 80mm;
            padding: 5mm;
        }
        .center {
            text-align: center;
        }
        .bold {
            font-weight: bold;
        }
        .line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .double-line {
            border-top: 2px solid #000;
            margin: 5px 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        .header {
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 3px;
        }
        .header p {
            font-size: 10px;
            margin: 1px 0;
        }
        .section-title {
            font-weight: bold;
            font-size: 12px;
            margin: 8px 0 4px 0;
        }
        .item-row {
            margin: 3px 0;
        }
        .item-name {
            font-weight: bold;
        }
        .qty {
            display: inline-block;
            width: 30px;
            text-align: center;
        }
        .total-section {
            margin-top: 10px;
        }
        .grand-total {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
        }
        .footer {
            margin-top: 15px;
            font-size: 10px;
        }
        @media print {
            body {
                width: 80mm;
            }
            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header center">
        <h1>PS RENTAL</h1>
        <p>Gaming Center</p>
        <p>Jl. Contoh No. 123</p>
        <p>Tel: (0361) 123456</p>
    </div>

    <div class="double-line"></div>

    <!-- Session Info -->
    <div class="center">
        <p class="bold">SESSION #{{ $rentalSession->id }}</p>
        <p>{{ now()->format('d M Y, H:i:s') }}</p>
    </div>

    <div class="line"></div>

    <!-- Customer & Staff -->
    <p>Customer: {{ $rentalSession->customer_name ?? 'Walk-in' }}</p>
    <p>Cashier: {{ $rentalSession->user->name }}</p>

    <div class="double-line"></div>

    <!-- Console Rental -->
    <p class="section-title">CONSOLE RENTAL</p>
    <p class="item-name">{{ $rentalSession->console->console_number }} - {{ $rentalSession->console->consoleType->name }}</p>
    <div class="row">
        <span>Start:</span>
        <span>{{ $rentalSession->start_time->format('H:i') }}</span>
    </div>
    @if($rentalSession->end_time)
        <div class="row">
            <span>End:</span>
            <span>{{ $rentalSession->end_time->format('H:i') }}</span>
        </div>
    @endif
    @if($rentalSession->package)
        <div class="row">
            <span>Package:</span>
            <span>{{ $rentalSession->package->name }}</span>
        </div>
    @endif
    <div class="row">
        <span>Duration:</span>
        <span>
                @if($rentalSession->end_time)
                {{ $rentalSession->end_time->diffInMinutes($rentalSession->start_time) - $rentalSession->total_paused_minutes }} mins
            @else
                Ongoing
            @endif
            </span>
    </div>
    <div class="line"></div>
    <div class="row bold">
        <span>Console Charges:</span>
        <span>Rp {{ number_format($rentalSession->total_cost, 0, ',', '.') }}</span>
    </div>

    <!-- Food & Beverage -->
    @if($foodOrders->count() > 0)
        <div class="double-line"></div>
        <p class="section-title">FOOD & BEVERAGE</p>
        @php $foodTotal = 0; @endphp
        @foreach($foodOrders as $order)
            @foreach($order->items as $item)
                <div class="item-row">
                    <div class="row">
                        <span><span class="qty">{{ $item->quantity }}x</span> {{ $item->foodItem->name }}</span>
                        <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div style="font-size: 9px; color: #666; margin-left: 35px;">
                        @ Rp {{ number_format($item->price, 0, ',', '.') }}
                    </div>
                </div>
                @php $foodTotal += $item->subtotal; @endphp
            @endforeach
        @endforeach
        <div class="line"></div>
        <div class="row bold">
            <span>F&B Charges:</span>
            <span>Rp {{ number_format($foodTotal, 0, ',', '.') }}</span>
        </div>
    @else
        @php $foodTotal = 0; @endphp
    @endif

    <!-- Totals -->
    <div class="double-line"></div>
    <div class="total-section">
        @php
            $subtotal = $rentalSession->total_cost + $foodTotal;
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
    </div>

    <!-- Payment Status -->
    @if($rentalSession->invoice && $rentalSession->invoice->payment_status === 'paid')
        <div class="double-line"></div>
        <div class="center bold">
            PAID - {{ strtoupper($rentalSession->invoice->payment_method) }}
        </div>
        <div class="center" style="font-size: 10px;">
            {{ $rentalSession->invoice->paid_at->format('d M Y, H:i') }}
        </div>
    @else
        <div class="double-line"></div>
        <div class="center bold">
            PAYMENT PENDING
        </div>
    @endif

    <!-- Footer -->
    <div class="double-line"></div>
    <div class="footer center">
        <p>Thank you for your visit!</p>
        <p>Please come again</p>
        <p style="margin-top: 5px;">www.psrental.com</p>
    </div>
</body>
</html>
