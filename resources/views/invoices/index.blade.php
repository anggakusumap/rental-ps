@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Invoices</h1>
                <p class="text-gray-600 mt-1">Manage all billing and payments</p>
            </div>
            <a href="{{ route('invoices.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition shadow-lg hover:shadow-xl flex items-center">
                <i class="fas fa-plus mr-2"></i>Create Invoice
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Invoices</p>
                    <p class="text-3xl font-bold mt-2">{{ $invoices->total() }}</p>
                </div>
                <i class="fas fa-file-invoice text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Paid</p>
                    <p class="text-3xl font-bold mt-2">{{ $invoices->where('payment_status', 'paid')->count() }}</p>
                </div>
                <i class="fas fa-check-circle text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Unpaid</p>
                    <p class="text-3xl font-bold mt-2">{{ $invoices->where('payment_status', 'unpaid')->count() }}</p>
                </div>
                <i class="fas fa-exclamation-circle text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Revenue</p>
                    <p class="text-2xl font-bold mt-2">Rp {{ number_format($invoices->where('payment_status', 'paid')->sum('total'), 0, ',', '.') }}</p>
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
                            <div class="text-sm font-medium text-gray-900">{{ $invoice->customer_name ?? 'Walk-in Customer' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($invoice->rentalSession && $invoice->order)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-layer-group mr-1"></i>Combined
                            </span>
                            @elseif($invoice->rentalSession)
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
                            <div class="text-xs text-gray-500">incl. Rp {{ number_format($invoice->tax, 0, ',', '.') }} tax</div>
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
                                </div>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                <i class="fas fa-clock mr-1"></i>UNPAID
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('invoices.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                    View
                                </a>
                                <a href="{{ route('invoices.pdf', $invoice) }}" class="text-red-600 hover:text-red-900 font-medium text-sm">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mb-4">
                                    <i class="fas fa-file-invoice text-3xl text-indigo-600"></i>
                                </div>
                                <p class="text-gray-500 text-lg font-medium">No invoices found</p>
                                <p class="text-gray-400 text-sm mt-1">Create your first invoice</p>
                                <a href="{{ route('invoices.create') }}" class="mt-4 bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                                    Create Invoice
                                </a>
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
@endsection
