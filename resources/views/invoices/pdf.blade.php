<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #4F46E5;
        }
        .company-info h1 {
            color: #4F46E5;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-number {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .customer-info {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f8f9fa;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #dee2e6;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        .totals table td {
            border: none;
            padding: 5px 10px;
        }
        .total-row {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #333 !important;
        }
        .payment-status {
            margin-top: 150px;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }
        .paid {
            background-color: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }
        .unpaid {
            background-color: #fff3cd;
            color: #856404;
            border: 2px solid #ffeaa7;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            color: #4F46E5;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="company-info">
        <h1>The Room PlayStation</h1>
        <p>Gaming Center</p>
        <p>Address Line 1</p>
        <p>City, State 12345</p>
        <p>Phone: (123) 456-7890</p>
    </div>
    <div class="invoice-info">
        <div class="invoice-number">{{ $invoice->invoice_number }}</div>
        <p><strong>Date:</strong> {{ $invoice->created_at->format('d M Y') }}</p>
        <p><strong>Time:</strong> {{ $invoice->created_at->format('H:i') }}</p>
        <p><strong>Cashier:</strong> {{ $invoice->user->name }}</p>
    </div>
</div>

<div class="customer-info">
    <strong>CUSTOMER:</strong> {{ $invoice->customer_name ?? 'Walk-in Customer' }}
</div>

@if($invoice->rentalSession)
    <div class="section-title">CONSOLE RENTAL</div>
    <table>
        <thead>
        <tr>
            <th>Description</th>
            <th class="text-center">Duration</th>
            <th class="text-right">Amount</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <strong>{{ $invoice->rentalSession->console->console_number }}</strong><br>
                {{ $invoice->rentalSession->console->consoleType->name }}<br>
                <small>
                    {{ $invoice->rentalSession->start_time->format('d M Y, H:i') }} -
                    {{ $invoice->rentalSession->end_time?->format('H:i') }}
                </small>
                @if($invoice->rentalSession->package)
                    <br><small>Package: {{ $invoice->rentalSession->package->name }}</small>
                @endif
            </td>
            <td class="text-center">
                @php
                    $duration = $invoice->rentalSession->end_time
                        ? $invoice->rentalSession->end_time->diffInMinutes($invoice->rentalSession->start_time) - $invoice->rentalSession->total_paused_minutes
                        : 0;
                @endphp
                {{ $duration }} mins
            </td>
            <td class="text-right">Rp {{ number_format($invoice->console_charges, 0, ',', '.') }}</td>
        </tr>
        </tbody>
    </table>
@endif

@if($invoice->order)
    <div class="section-title">FOOD & BEVERAGE</div>
    <table>
        <thead>
        <tr>
            <th>Item</th>
            <th class="text-center">Qty</th>
            <th class="text-right">Price</th>
            <th class="text-right">Subtotal</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->order->items as $item)
            <tr>
                <td>{{ $item->foodItem->name }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

<div class="totals">
    <table>
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Tax (10%):</td>
            <td class="text-right">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td>TOTAL:</td>
            <td class="text-right">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
        </tr>
    </table>
</div>

<div class="payment-status {{ $invoice->payment_status === 'paid' ? 'paid' : 'unpaid' }}">
    @if($invoice->payment_status === 'paid')
        ✓ PAID - {{ strtoupper($invoice->payment_method) }}
        <br>
        <small>{{ $invoice->paid_at->format('d M Y, H:i') }}</small>
    @else
        ⏳ PAYMENT PENDING
    @endif
</div>

<div style="margin-top: 50px; text-align: center; font-size: 10px; color: #666;">
    <p>Thank you for your business!</p>
    <p>Please keep this invoice for your records.</p>
</div>
</body>
</html>
