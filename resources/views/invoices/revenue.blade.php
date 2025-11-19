@extends('layouts.app')

@section('title', 'Revenue Report')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Revenue Report</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div>
                <label class="block text-sm mb-1">Type</label>
                <select name="type" class="border rounded px-3 py-2">
                    <option value="daily" {{ $type === 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="monthly" {{ $type === 'monthly' ? 'selected' : '' }}>Monthly</option>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="border rounded px-3 py-2">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-500 text-sm">Total Revenue</p>
            <p class="text-2xl font-bold">Rp {{ number_format($totals['total_revenue'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-500 text-sm">Console Revenue</p>
            <p class="text-2xl font-bold">Rp {{ number_format($totals['console_revenue'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-500 text-sm">Food Revenue</p>
            <p class="text-2xl font-bold">Rp {{ number_format($totals['food_revenue'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-500 text-sm">Invoices</p>
            <p class="text-2xl font-bold">{{ $totals['invoices_count'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">{{ $type === 'daily' ? 'Date' : 'Month' }}</th>
                <th class="px-6 py-3 text-right">Total Revenue</th>
                <th class="px-6 py-3 text-right">Console</th>
                <th class="px-6 py-3 text-right">Food</th>
            </tr>
            </thead>
            <tbody class="divide-y">
            @foreach($data as $row)
                <tr>
                    <td class="px-6 py-4">{{ $type === 'daily' ? $row->date : $row->month }}</td>
                    <td class="px-6 py-4 text-right font-medium">Rp {{ number_format($row->revenue, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right">Rp {{ number_format($row->console_revenue, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right">Rp {{ number_format($row->food_revenue, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
