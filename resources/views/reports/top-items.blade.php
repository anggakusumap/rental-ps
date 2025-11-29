@extends('layouts.app')

@section('title', 'Top Selling Items')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Top Selling Items</h1>
                <p class="text-gray-600 mt-1">Best performing food and beverage items</p>
            </div>
            <a href="{{ route('reports.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Reports
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
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
        </form>
    </div>

    <!-- Top Items List -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-orange-500 to-pink-600 text-white">
            <h2 class="text-2xl font-bold flex items-center">
                <i class="fas fa-trophy mr-3 text-yellow-300"></i>
                Top 10 Best Sellers
            </h2>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($topFoodItems as $index => $item)
                <div class="p-6 hover:bg-gray-50 transition">
                    <div class="flex items-center">
                        <!-- Rank Badge -->
                        <div class="flex-shrink-0 mr-6">
                            @if($index === 0)
                                <div class="w-16 h-16 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center shadow-lg">
                                    <span class="text-2xl font-bold text-white">1</span>
                                </div>
                            @elseif($index === 1)
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-300 to-gray-500 rounded-full flex items-center justify-center shadow-lg">
                                    <span class="text-2xl font-bold text-white">2</span>
                                </div>
                            @elseif($index === 2)
                                <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center shadow-lg">
                                    <span class="text-2xl font-bold text-white">3</span>
                                </div>
                            @else
                                <div class="w-16 h-16 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                                    <span class="text-2xl font-bold text-white">{{ $index + 1 }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Item Info -->
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-800">{{ $item->name }}</h3>
                            <div class="flex items-center mt-2 space-x-6">
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-shopping-cart mr-2 text-blue-500"></i>
                                    <span class="font-semibold">{{ $item->total_quantity }}</span>
                                    <span class="text-sm ml-1">sold</span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-money-bill-wave mr-2 text-green-500"></i>
                                    <span class="font-semibold">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</span>
                                    <span class="text-sm ml-1">revenue</span>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="flex-shrink-0 ml-6 w-48">
                            @php
                                $maxRevenue = $topFoodItems->max('total_revenue');
                                $percentage = $maxRevenue > 0 ? ($item->total_revenue / $maxRevenue) * 100 : 0;
                            @endphp
                            <div class="text-right mb-1">
                                <span class="text-sm font-medium text-gray-600">{{ number_format($percentage, 0) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-3 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-utensils text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No Sales Data</h3>
                    <p class="text-gray-600">No food items sold during the selected period</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Summary Stats -->
    @if($topFoodItems->isNotEmpty())
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Items Sold</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($topFoodItems->sum('total_quantity')) }}</p>
                    </div>
                    <i class="fas fa-shopping-cart text-4xl opacity-50"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Total F&B Revenue</p>
                        <p class="text-2xl font-bold mt-2">Rp {{ number_format($topFoodItems->sum('total_revenue'), 0, ',', '.') }}</p>
                    </div>
                    <i class="fas fa-money-bill-wave text-4xl opacity-50"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Average per Item</p>
                        <p class="text-2xl font-bold mt-2">Rp {{ $topFoodItems->count() > 0 ? number_format($topFoodItems->sum('total_revenue') / $topFoodItems->count(), 0, ',', '.') : 0 }}</p>
                    </div>
                    <i class="fas fa-chart-bar text-4xl opacity-50"></i>
                </div>
            </div>
        </div>
    @endif
@endsection
