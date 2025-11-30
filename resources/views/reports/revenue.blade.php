@extends('layouts.app')

@section('title', 'Revenue Report')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Revenue Report</h1>
                <p class="text-gray-600 mt-1">Track daily and monthly revenue with detailed breakdowns</p>
            </div>
            <a href="{{ route('reports.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Reports
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Revenue</p>
                    <p class="text-2xl font-bold mt-2">Rp {{ number_format($totals['total_revenue'], 0, ',', '.') }}</p>
                </div>
                <i class="fas fa-dollar-sign text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Console Revenue</p>
                    <p class="text-2xl font-bold mt-2">Rp {{ number_format($totals['console_revenue'], 0, ',', '.') }}</p>
                </div>
                <i class="fas fa-gamepad text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Food Revenue</p>
                    <p class="text-2xl font-bold mt-2">Rp {{ number_format($totals['food_revenue'], 0, ',', '.') }}</p>
                </div>
                <i class="fas fa-utensils text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Invoices</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($totals['invoices_count']) }}</p>
                </div>
                <i class="fas fa-file-invoice text-4xl opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                <select name="type" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                    <option value="daily" {{ $type === 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="monthly" {{ $type === 'monthly' ? 'selected' : '' }}>Monthly</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                <i class="fas fa-filter mr-2"></i>Apply Filter
            </button>
            <a href="{{ route('reports.export', ['type' => 'revenue', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-medium">
                <i class="fas fa-download mr-2"></i>Export CSV
            </a>
        </form>
    </div>

    <!-- Revenue Chart Visual -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Revenue Breakdown</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @php
                $total = $totals['total_revenue'] > 0 ? $totals['total_revenue'] : 1;
                $consolePercentage = ($totals['console_revenue'] / $total) * 100;
                $foodPercentage = ($totals['food_revenue'] / $total) * 100;
            @endphp

            <div class="text-center">
                <div class="relative w-32 h-32 mx-auto">
                    <svg class="transform -rotate-90" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="50" fill="none" stroke="#e5e7eb" stroke-width="20"/>
                        <circle cx="60" cy="60" r="50" fill="none" stroke="#3b82f6" stroke-width="20"
                                stroke-dasharray="{{ $consolePercentage * 3.14 }} 314"
                                stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-2xl font-bold text-gray-800">{{ number_format($consolePercentage, 0) }}%</span>
                    </div>
                </div>
                <p class="text-gray-600 font-medium mt-4">Console Revenue</p>
                <p class="text-sm text-gray-500">Rp {{ number_format($totals['console_revenue'], 0, ',', '.') }}</p>
            </div>

            <div class="text-center">
                <div class="relative w-32 h-32 mx-auto">
                    <svg class="transform -rotate-90" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="50" fill="none" stroke="#e5e7eb" stroke-width="20"/>
                        <circle cx="60" cy="60" r="50" fill="none" stroke="#f97316" stroke-width="20"
                                stroke-dasharray="{{ $foodPercentage * 3.14 }} 314"
                                stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-2xl font-bold text-gray-800">{{ number_format($foodPercentage, 0) }}%</span>
                    </div>
                </div>
                <p class="text-gray-600 font-medium mt-4">Food & Beverage</p>
                <p class="text-sm text-gray-500">Rp {{ number_format($totals['food_revenue'], 0, ',', '.') }}</p>
            </div>

            <div class="text-center">
                <div class="relative w-32 h-32 mx-auto bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-coins text-4xl text-green-600 mb-2"></i>
                        <p class="text-sm font-medium text-green-800">Total</p>
                    </div>
                </div>
                <p class="text-gray-600 font-medium mt-4">Combined Revenue</p>
                <p class="text-sm text-gray-500">Rp {{ number_format($totals['total_revenue'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Revenue Data Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-500 to-blue-600 text-white">
            <h2 class="text-2xl font-bold">{{ ucfirst($type) }} Revenue Data</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $type === 'daily' ? 'Date' : 'Month' }}
                    </th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Console Revenue
                    </th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Food Revenue
                    </th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total Revenue
                    </th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Invoices
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($data as $row)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            @if($type === 'daily')
                                {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}
                            @else
                                {{ \Carbon\Carbon::parse($row->month . '-01')->format('M Y') }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-right">
                            Rp {{ number_format($row->console_revenue ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-right">
                            Rp {{ number_format($row->food_revenue ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">
                            Rp {{ number_format($row->revenue ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-right">
                                <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full font-medium">
                                    {{ $row->invoice_count ?? 0 }}
                                </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-chart-line text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">No Data Available</h3>
                            <p class="text-gray-600">No revenue data found for the selected period</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
                @if($data->isNotEmpty())
                    <tfoot class="bg-gray-100 border-t-2 border-gray-300">
                    <tr>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">
                            TOTAL
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                            Rp {{ number_format($totals['console_revenue'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                            Rp {{ number_format($totals['food_revenue'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-green-600 text-right">
                            Rp {{ number_format($totals['total_revenue'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                                <span class="bg-indigo-600 text-white px-3 py-1 rounded-full">
                                    {{ $totals['invoices_count'] }}
                                </span>
                        </td>
                    </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
