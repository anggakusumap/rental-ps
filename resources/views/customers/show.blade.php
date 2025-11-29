@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('customers.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Customers
            </a>
            <div class="flex justify-between items-start mt-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">{{ $customer->name }}</h1>
                    <p class="text-gray-600 mt-1">Customer Profile & History</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('customers.edit', $customer) }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold shadow-lg">
                        <i class="fas fa-edit mr-2"></i>Edit Customer
                    </a>
                </div>
            </div>
        </div>

        <!-- Customer Info Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Contact Information -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user text-indigo-600 mr-3"></i>
                    Contact Information
                </h2>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-500 font-medium">Full Name</label>
                        <p class="text-lg font-semibold text-gray-800 mt-1">{{ $customer->name }}</p>
                    </div>

                    @if($customer->phone)
                        <div>
                            <label class="text-sm text-gray-500 font-medium">Phone Number</label>
                            <p class="text-lg font-semibold text-gray-800 mt-1 flex items-center">
                                <i class="fas fa-phone text-green-600 mr-2"></i>
                                {{ $customer->phone }}
                            </p>
                        </div>
                    @endif

                    @if($customer->email)
                        <div>
                            <label class="text-sm text-gray-500 font-medium">Email Address</label>
                            <p class="text-lg font-semibold text-gray-800 mt-1 flex items-center">
                                <i class="fas fa-envelope text-blue-600 mr-2"></i>
                                {{ $customer->email }}
                            </p>
                        </div>
                    @endif

                    @if($customer->address)
                        <div>
                            <label class="text-sm text-gray-500 font-medium">Address</label>
                            <p class="text-gray-700 mt-1">{{ $customer->address }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="text-sm text-gray-500 font-medium">Status</label>
                        <div class="mt-1">
                            @if($customer->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    <i class="fas fa-ban mr-1"></i>Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="lg:col-span-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <h2 class="text-xl font-bold mb-6 flex items-center">
                    <i class="fas fa-chart-bar mr-3"></i>
                    Customer Statistics
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <i class="fas fa-gamepad text-2xl"></i>
                        </div>
                        <p class="text-3xl font-bold">{{ $customer->rentalSessions->count() }}</p>
                        <p class="text-indigo-100 text-sm mt-1">Gaming Sessions</p>
                    </div>

                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <i class="fas fa-shopping-cart text-2xl"></i>
                        </div>
                        <p class="text-3xl font-bold">{{ $customer->orders->count() }}</p>
                        <p class="text-indigo-100 text-sm mt-1">Food Orders</p>
                    </div>

                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <i class="fas fa-clock text-2xl"></i>
                        </div>
                        <p class="text-3xl font-bold">
                            @php
                                $totalMinutes = $customer->rentalSessions
                                    ->where('status', 'completed')
                                    ->sum(function($session) {
                                        return $session->end_time ? $session->end_time->diffInMinutes($session->start_time) - $session->total_paused_minutes : 0;
                                    });
                                $hours = floor($totalMinutes / 60);
                            @endphp
                            {{ $hours }}h
                        </p>
                        <p class="text-indigo-100 text-sm mt-1">Total Gaming Time</p>
                    </div>

                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <i class="fas fa-money-bill-wave text-2xl"></i>
                        </div>
                        <p class="text-2xl font-bold">
                            Rp {{ number_format($customer->rentalSessions->where('status', 'completed')->sum('total_cost'), 0, ',', '.') }}
                        </p>
                        <p class="text-indigo-100 text-sm mt-1">Total Spent</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes Section -->
        @if($customer->notes)
            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-6 mb-8">
                <h3 class="font-semibold text-yellow-900 mb-2 flex items-center">
                    <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>
                    Customer Notes
                </h3>
                <p class="text-yellow-800">{{ $customer->notes }}</p>
            </div>
        @endif

        <!-- Rental Sessions History -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-6 bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-gamepad mr-3"></i>
                    Gaming Sessions History ({{ $customer->rentalSessions->count() }})
                </h2>
            </div>

            @if($customer->rentalSessions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Session ID</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Console</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Duration</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Cost</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($customer->rentalSessions->sortByDesc('created_at') as $session)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">#{{ $session->id }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $session->console->console_number }}</div>
                                    <div class="text-xs text-gray-500">{{ $session->console->consoleType->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $session->start_time->format('M d, Y') }}<br>
                                    <span class="text-xs text-gray-500">{{ $session->start_time->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @if($session->end_time)
                                        {{ $session->end_time->diffInMinutes($session->start_time) - $session->total_paused_minutes }} min
                                    @else
                                        <span class="text-gray-500">Ongoing</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($session->status === 'active')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-play mr-1"></i>Active
                                    </span>
                                    @elseif($session->status === 'paused')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-pause mr-1"></i>Paused
                                    </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        <i class="fas fa-check mr-1"></i>Completed
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    @if($session->status === 'completed')
                                        Rp {{ number_format($session->total_cost, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('rental-sessions.show', $session) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                        View Details →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-gamepad text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No Gaming Sessions Yet</h3>
                    <p class="text-gray-600">This customer hasn't started any gaming sessions</p>
                </div>
            @endif
        </div>

        <!-- Food Orders History -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-orange-500 to-orange-600 text-white">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-shopping-cart mr-3"></i>
                    Food & Beverage Orders ({{ $customer->orders->count() }})
                </h2>
            </div>

            @if($customer->orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Order #</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Items</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($customer->orders->sortByDesc('created_at') as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $order->created_at->format('M d, Y') }}<br>
                                    <span class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $order->items->count() }} items
                                </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
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
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shopping-cart text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No Food Orders Yet</h3>
                    <p class="text-gray-600">This customer hasn't placed any food orders</p>
                </div>
            @endif
        </div>
    </div>
@endsection
