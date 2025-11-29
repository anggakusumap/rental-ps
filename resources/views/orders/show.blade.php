{{-- resources/views/orders/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('orders.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Orders
            </a>
            <div class="flex justify-between items-start mt-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Order Details</h1>
                    <p class="text-gray-600 mt-1">{{ $order->order_number }}</p>
                </div>
                <div class="flex space-x-3">
                    @if($order->payment_status === 'unpaid')
                        <button onclick="document.getElementById('paymentModal').classList.remove('hidden')" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-semibold shadow-lg">
                            <i class="fas fa-check mr-2"></i>Mark as Paid
                        </button>
                    @endif
                    <a href="{{ route('invoices.create', ['order_id' => $order->id]) }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold shadow-lg">
                        <i class="fas fa-file-invoice mr-2"></i>Generate Invoice
                    </a>
                </div>
            </div>
        </div>

        <!-- Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Order Status -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600 font-medium">Order Status</span>
                    @if($order->status === 'completed')
                        <i class="fas fa-check-circle text-2xl text-green-500"></i>
                    @elseif($order->status === 'pending')
                        <i class="fas fa-clock text-2xl text-yellow-500"></i>
                    @else
                        <i class="fas fa-times-circle text-2xl text-red-500"></i>
                    @endif
                </div>
                <p class="text-2xl font-bold {{ $order->status === 'completed' ? 'text-green-600' : ($order->status === 'pending' ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ ucfirst($order->status) }}
                </p>
            </div>

            <!-- Payment Status -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600 font-medium">Payment Status</span>
                    @if($order->payment_status === 'paid')
                        <i class="fas fa-check-circle text-2xl text-green-500"></i>
                    @else
                        <i class="fas fa-exclamation-circle text-2xl text-red-500"></i>
                    @endif
                </div>
                <p class="text-2xl font-bold {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-red-600' }}">
                    {{ ucfirst($order->payment_status) }}
                </p>
                @if($order->payment_method)
                    <p class="text-sm text-gray-500 mt-1">via {{ ucfirst($order->payment_method) }}</p>
                @endif
            </div>

            <!-- Order Total -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-indigo-100 font-medium">Order Total</span>
                    <i class="fas fa-money-bill-wave text-2xl text-white opacity-50"></i>
                </div>
                <p class="text-3xl font-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                <p class="text-sm text-indigo-100 mt-1">incl. Rp {{ number_format($order->tax, 0, ',', '.') }} tax</p>
            </div>
        </div>

        <!-- Order Information -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Customer & Order Details -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-info-circle text-indigo-600 mr-3"></i>
                    Order Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm text-gray-500 font-medium">Order Number</label>
                        <p class="text-lg font-semibold text-gray-800 mt-1">{{ $order->order_number }}</p>
                    </div>

                    <div>
                        <label class="text-sm text-gray-500 font-medium">Order Date</label>
                        <p class="text-lg font-semibold text-gray-800 mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>

                    <div>
                        <label class="text-sm text-gray-500 font-medium">Customer Name</label>
                        <p class="text-lg font-semibold text-gray-800 mt-1">{{ $order->customer_name ?? 'Walk-in Customer' }}</p>
                    </div>

                    <div>
                        <label class="text-sm text-gray-500 font-medium">Created By</label>
                        <p class="text-lg font-semibold text-gray-800 mt-1">{{ $order->user->name }}</p>
                    </div>
                </div>

                @if($order->rentalSession)
                    <div class="mt-6 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-link text-blue-600 text-xl mr-3 mt-1"></i>
                            <div>
                                <h3 class="font-semibold text-blue-900">Linked to Rental Session</h3>
                                <p class="text-sm text-blue-700 mt-1">Session #{{ $order->rentalSession->id }} - {{ $order->rentalSession->console->console_number }}</p>
                                <a href="{{ route('rental-sessions.show', $order->rentalSession) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 inline-block">
                                    View Session Details →
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-chart-pie text-indigo-600 mr-3"></i>
                    Order Summary
                </h2>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-blue-500 rounded-lg p-2 mr-3">
                                <i class="fas fa-shopping-bag text-white"></i>
                            </div>
                            <span class="text-gray-700 font-medium">Total Items</span>
                        </div>
                        <span class="text-xl font-bold text-blue-600">{{ $order->items->count() }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-purple-500 rounded-lg p-2 mr-3">
                                <i class="fas fa-box text-white"></i>
                            </div>
                            <span class="text-gray-700 font-medium">Total Quantity</span>
                        </div>
                        <span class="text-xl font-bold text-purple-600">{{ $order->items->sum('quantity') }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-50 to-green-100 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-green-500 rounded-lg p-2 mr-3">
                                <i class="fas fa-receipt text-white"></i>
                            </div>
                            <span class="text-gray-700 font-medium">Subtotal</span>
                        </div>
                        <span class="text-xl font-bold text-green-600">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-utensils mr-3"></i>
                    Order Items
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Unit Price</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Subtotal</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $index => $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-gray-500 font-medium">{{ $index + 1 }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="bg-gradient-to-br from-orange-400 to-pink-500 rounded-lg p-3 mr-3">
                                        <i class="fas fa-utensils text-white"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $item->foodItem->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $item->foodItem->category->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center bg-indigo-100 text-indigo-800 text-sm font-semibold px-4 py-2 rounded-full">
                                {{ $item->quantity }}x
                            </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-gray-700 font-medium">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-lg font-bold text-indigo-600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Order Totals -->
            <div class="bg-gray-50 px-6 py-6 border-t-2 border-gray-200">
                <div class="flex justify-end">
                    <div class="w-80">
                        <div class="flex justify-between py-2 text-gray-700">
                            <span class="font-medium">Subtotal:</span>
                            <span class="font-semibold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-2 text-gray-700">
                            <span class="font-medium">Tax (10%):</span>
                            <span class="font-semibold">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-3 text-xl font-bold border-t-2 border-gray-300">
                            <span>Total:</span>
                            <span class="text-indigo-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>

                        @if($order->payment_status === 'paid')
                            <div class="mt-4 p-4 bg-green-100 border-2 border-green-300 rounded-lg text-center">
                                <p class="text-green-800 font-bold flex items-center justify-center">
                                    <i class="fas fa-check-circle mr-2 text-xl"></i>
                                    PAYMENT RECEIVED
                                </p>
                                <p class="text-sm text-green-700 mt-1">{{ ucfirst($order->payment_method) }}</p>
                            </div>
                        @else
                            <div class="mt-4 p-4 bg-red-100 border-2 border-red-300 rounded-lg text-center">
                                <p class="text-red-800 font-bold flex items-center justify-center">
                                    <i class="fas fa-exclamation-circle mr-2 text-xl"></i>
                                    PAYMENT PENDING
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center">
            <a href="{{ route('orders.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Orders
            </a>
            <div class="flex space-x-3">
                <button onclick="window.print()" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition font-semibold">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
                <a href="{{ route('invoices.create', ['order_id' => $order->id]) }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold">
                    <i class="fas fa-file-invoice mr-2"></i>Generate Invoice
                </a>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    @if($order->payment_status === 'unpaid')
        <div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4">
                <div class="text-center mb-6">
                    <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-dollar-sign text-3xl text-green-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800">Mark as Paid</h3>
                    <p class="text-gray-600 mt-2">Confirm payment for this order</p>
                </div>

                <form method="POST" action="{{ route('orders.mark-paid', $order->id) }}">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-3">Payment Method</label>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-green-500 transition">
                                <input type="radio" name="payment_method" value="cash" required class="w-5 h-5 text-green-600">
                                <i class="fas fa-money-bill-wave text-green-600 text-xl mx-3"></i>
                                <span class="font-medium text-gray-800">Cash</span>
                            </label>
                            <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition">
                                <input type="radio" name="payment_method" value="card" class="w-5 h-5 text-blue-600">
                                <i class="fas fa-credit-card text-blue-600 text-xl mx-3"></i>
                                <span class="font-medium text-gray-800">Card</span>
                            </label>
                            <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-purple-500 transition">
                                <input type="radio" name="payment_method" value="transfer" class="w-5 h-5 text-purple-600">
                                <i class="fas fa-exchange-alt text-purple-600 text-xl mx-3"></i>
                                <span class="font-medium text-gray-800">Bank Transfer</span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium">Amount to Receive:</span>
                            <span class="text-2xl font-bold text-green-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <button type="submit" class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-semibold shadow-lg">
                            <i class="fas fa-check mr-2"></i>Confirm Payment
                        </button>
                        <button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')" class="flex-1 bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition font-semibold">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .max-w-5xl, .max-w-5xl * {
                visibility: visible;
            }
            .max-w-5xl {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            button, a[href] {
                display: none !important;
            }
        }
    </style>
@endsection
