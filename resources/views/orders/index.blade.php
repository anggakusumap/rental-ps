@extends('layouts.app')

@section('title', 'Food Orders')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Food & Beverage Orders</h1>
                <p class="text-gray-600 mt-1">Track all customer orders</p>
            </div>
            <a href="{{ route('orders.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition shadow-lg hover:shadow-xl flex items-center">
                <i class="fas fa-plus mr-2"></i>New Order
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Orders</p>
                    <p class="text-3xl font-bold mt-2">{{ $orders->total() }}</p>
                </div>
                <i class="fas fa-shopping-cart text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Pending</p>
                    <p class="text-3xl font-bold mt-2">{{ $orders->where('status', 'pending')->count() }}</p>
                </div>
                <i class="fas fa-clock text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Completed</p>
                    <p class="text-3xl font-bold mt-2">{{ $orders->where('status', 'completed')->count() }}</p>
                </div>
                <i class="fas fa-check-circle text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Revenue</p>
                    <p class="text-2xl font-bold mt-2">Rp {{ number_format($orders->where('payment_status', 'paid')->sum('total'), 0, ',', '.') }}</p>
                </div>
                <i class="fas fa-money-bill-wave text-4xl opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Order #</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Items</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Payment</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="bg-indigo-100 rounded-lg p-2 mr-3">
                                    <i class="fas fa-receipt text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $order->order_number }}</p>
                                    @if($order->rentalSession)
                                        <p class="text-xs text-gray-500">
                                            <i class="fas fa-link mr-1"></i>Session #{{ $order->rentalSession->id }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $order->customer_name ?? 'Walk-in' }}</div>
                            <div class="text-xs text-gray-500">by {{ $order->user->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                            <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-1 rounded-full">
                                {{ $order->items->count() }} items
                            </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                            <div class="text-xs text-gray-500">+Rp {{ number_format($order->tax, 0, ',', '.') }} tax</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $order->created_at->format('M d, Y') }}<br>
                            <span class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($order->status === 'completed')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>Completed
                            </span>
                            @elseif($order->status === 'pending')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>Pending
                            </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                {{ ucfirst($order->status) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($order->payment_status === 'paid')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Paid
                            </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-circle mr-1"></i>Unpaid
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                View Details →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-orange-100 rounded-full w-16 h-16 flex items-center justify-center mb-4">
                                    <i class="fas fa-shopping-cart text-3xl text-orange-600"></i>
                                </div>
                                <p class="text-gray-500 text-lg font-medium">No orders found</p>
                                <p class="text-gray-400 text-sm mt-1">Create your first order</p>
                                <a href="{{ route('orders.create') }}" class="mt-4 bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                                    New Order
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
