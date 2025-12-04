@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Invoices</h1>
                <p class="text-gray-600 mt-1">All payment records and receipts</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Date</label>
                <input
                    type="date"
                    name="date"
                    value="{{ $date }}"
                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition"
                >
            </div>

            <button type="submit"
                    class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                <i class="fas fa-filter mr-2"></i>Apply Filter
            </button>
        </form>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Console Only</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['consoleOnly'] }}</p>
                    <p class="text-blue-100 text-xs mt-1">Rp {{ number_format($stats['consoleOnlyRevenue'], 0, ',', '.') }}</p>
                </div>
                <i class="fas fa-gamepad text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">F&B Only</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['foodOnly'] }}</p>
                    <p class="text-orange-100 text-xs mt-1">Rp {{ number_format($stats['foodOnlyRevenue'], 0, ',', '.') }}</p>
                </div>
                <i class="fas fa-utensils text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Console + F&B</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['combined'] }}</p>
                    <p class="text-purple-100 text-xs mt-1">Rp {{ number_format($stats['combinedRevenue'], 0, ',', '.') }}</p>
                </div>
                <i class="fas fa-layer-group text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Revenue</p>
                    <p class="text-2xl font-bold mt-2">Rp {{ number_format($stats['totalRevenue'], 0, ',', '.') }}</p>
                    <p class="text-green-100 text-xs mt-1">{{ $stats['totalInvoices'] }} invoices</p>
                </div>
                <i class="fas fa-money-bill-wave text-4xl opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Invoice #</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Session</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Console</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">F&B</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="bg-indigo-100 rounded-lg p-2 mr-3">
                                    <i class="fas fa-file-invoice text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $invoice->invoice_number }}</p>
                                    <p class="text-xs text-gray-500">by {{ $invoice->user->name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($invoice->rentalSession)
                                <a href="{{ route('rental-sessions.show', $invoice->rentalSession) }}" class="text-indigo-600 hover:underline font-medium">
                                    Session #{{ $invoice->rentalSession->id }}
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $invoice->customer_name ?? 'Walk-in' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($invoice->console_charges > 0 && $invoice->food_charges > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-layer-group mr-1"></i>Console + F&B
                                </span>
                            @elseif($invoice->console_charges > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-gamepad mr-1"></i>Console Only
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    <i class="fas fa-utensils mr-1"></i>F&B Only
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            @if($invoice->console_charges > 0)
                                <span class="font-semibold">Rp {{ number_format($invoice->console_charges, 0, ',', '.') }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            @if($invoice->food_charges > 0)
                                <span class="font-semibold">Rp {{ number_format($invoice->food_charges, 0, ',', '.') }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-base font-bold text-gray-900">Rp {{ number_format($invoice->total, 0, ',', '.') }}</div>
                            <div class="text-xs text-gray-500">+Rp {{ number_format($invoice->tax, 0, ',', '.') }} tax</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $invoice->created_at->format('M d, Y') }}<br>
                            <span class="text-xs text-gray-500">{{ $invoice->created_at->format('H:i') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($invoice->payment_status === 'paid')
                                <div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 mb-1">
                                        <i class="fas fa-check-circle mr-1"></i>PAID
                                    </span>
                                    <div class="text-xs text-gray-500">
                                        {{ ucfirst($invoice->payment_method) }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $invoice->paid_at->format('M d, H:i') }}
                                    </div>
                                </div>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    <i class="fas fa-clock mr-1"></i>UNPAID
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col space-y-2">
                                @if($invoice->rentalSession)
                                    <a href="{{ route('rental-sessions.print-receipt', $invoice->rentalSession) }}" class="text-purple-600 hover:text-purple-900 font-medium text-sm">
                                        <i class="fas fa-print mr-1"></i>Print
                                    </a>
                                @elseif($invoice->order)
                                    <a href="{{ route('orders.print-receipt', $invoice->order) }}" class="text-purple-600 hover:text-purple-900 font-medium text-sm">
                                        <i class="fas fa-print mr-1"></i>Print
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mb-4">
                                    <i class="fas fa-file-invoice text-3xl text-indigo-600"></i>
                                </div>
                                <p class="text-gray-500 text-lg font-medium">No invoices found</p>
                                <p class="text-gray-400 text-sm mt-1">Invoices are created automatically when payments are processed</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    <!-- Info Box -->
    <div class="mt-8 bg-blue-50 border-2 border-blue-200 rounded-xl p-6">
        <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
            About Invoices
        </h3>
        <ul class="space-y-2 text-sm text-blue-800">
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                <span>Invoices are automatically generated when payments are processed</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                <span>Each rental session can have one invoice that includes console and F&B charges</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                <span>Standalone food orders (without rental session) create separate invoices</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                <span>All receipts use thermal printer format for easy printing</span>
            </li>
        </ul>
    </div>
@endsection
