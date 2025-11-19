@extends('layouts.app')

@section('title', 'Invoice Details')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Invoice {{ $invoice->invoice_number }}</h1>
            <div class="flex space-x-3">
                <a href="{{ route('invoices.pdf', $invoice) }}" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                    <i class="fas fa-file-pdf mr-2"></i>Download PDF
                </a>
                @if($invoice->payment_status === 'unpaid')
                    <button onclick="document.getElementById('paymentModal').classList.remove('hidden')" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        <i class="fas fa-check mr-2"></i>Mark as Paid
                    </button>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-8">
            <div class="border-b pb-6 mb-6">
                <div class="flex justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-indigo-600">PS Rental</h2>
                        <p class="text-gray-600">Gaming Center</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Invoice Date</p>
                        <p class="font-medium">{{ $invoice->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-sm text-gray-600">Customer</p>
                <p class="font-medium text-lg">{{ $invoice->customer_name ?? 'Walk-in Customer' }}</p>
            </div>

            @if($invoice->rentalSession)
                <div class="mb-6">
                    <h3 class="font-semibold text-lg mb-3">Console Rental</h3>
                    <div class="border rounded">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left">Description</th>
                                <th class="px-4 py-2 text-right">Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="px-4 py-3">
                                    {{ $invoice->rentalSession->console->console_number }} - {{ $invoice->rentalSession->console->consoleType->name }}<br>
                                    <span class="text-sm text-gray-600">
                                    {{ $invoice->rentalSession->start_time->format('H:i') }} - {{ $invoice->rentalSession->end_time?->format('H:i') }}
                                </span>
                                </td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format($invoice->console_charges, 0, ',', '.') }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($invoice->order)
                <div class="mb-6">
                    <h3 class="font-semibold text-lg mb-3">Food & Beverage</h3>
                    <div class="border rounded">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left">Item</th>
                                <th class="px-4 py-2 text-center">Qty</th>
                                <th class="px-4 py-2 text-right">Price</th>
                                <th class="px-4 py-2 text-right">Subtotal</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($invoice->order->items as $item)
                                <tr class="border-t">
                                    <td class="px-4 py-3">{{ $item->foodItem->name }}</td>
                                    <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="border-t pt-6">
                <div class="flex justify-end">
                    <div class="w-64">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Tax (10%):</span>
                            <span class="font-medium">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold border-t pt-2">
                            <span>Total:</span>
                            <span class="text-indigo-600">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                        </div>

                        @if($invoice->payment_status === 'paid')
                            <div class="mt-4 p-3 bg-green-100 text-green-800 rounded text-center">
                                <i class="fas fa-check-circle mr-2"></i>PAID
                                <p class="text-sm">{{ $invoice->payment_method }} - {{ $invoice->paid_at->format('d M Y, H:i') }}</p>
                            </div>
                        @else
                            <div class="mt-4 p-3 bg-yellow-100 text-yellow-800 rounded text-center">
                                <i class="fas fa-clock mr-2"></i>UNPAID
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <h3 class="text-xl font-semibold mb-4">Mark Invoice as Paid</h3>
            <form method="POST" action="{{ route('invoices.mark-paid', $invoice) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Payment Method</label>
                    <select name="payment_method" required class="w-full border rounded px-3 py-2">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="transfer">Bank Transfer</option>
                    </select>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">Confirm Payment</button>
                    <button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')" class="bg-gray-300 px-6 py-2 rounded hover:bg-gray-400">Cancel</button>
                </div>
            </form>
        </div>
    </div>
@endsection
