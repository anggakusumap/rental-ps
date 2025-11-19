@extends('layouts.app')

@section('title', 'Create Invoice')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Create Invoice</h1>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('invoices.store') }}">
                @csrf

                @if($session)
                    <input type="hidden" name="rental_session_id" value="{{ $session->id }}">
                    <div class="mb-6 p-4 bg-blue-50 rounded">
                        <h3 class="font-semibold mb-2">Rental Session</h3>
                        <p>Console: {{ $session->console->console_number }}</p>
                        <p>Cost: Rp {{ number_format($session->total_cost, 0, ',', '.') }}</p>
                    </div>
                @endif

                @if($order)
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <div class="mb-6 p-4 bg-green-50 rounded">
                        <h3 class="font-semibold mb-2">Food Order</h3>
                        <p>Order #{{ $order->order_number }}</p>
                        <p>Total: Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                    </div>
                @endif

                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">Invoice Type</label>
                    <select name="invoice_type" required class="w-full border rounded px-3 py-2">
                        @if($session && $order)
                            <option value="combined">Combined (Console + Food)</option>
                        @endif
                        @if($session)
                            <option value="console_only">Console Only</option>
                        @endif
                        @if($order)
                            <option value="food_only">Food Only</option>
                        @endif
                    </select>
                </div>

                <div class="flex space-x-3">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                        <i class="fas fa-file-invoice mr-2"></i>Create Invoice
                    </button>
                    <a href="{{ route('invoices.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
