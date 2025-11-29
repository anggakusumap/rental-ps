@extends('layouts.app')

@section('title', 'Usage Statistics')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Usage Statistics</h1>
                <p class="text-gray-600 mt-1">Console utilization and session analytics</p>
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

    <!-- Console Type Usage -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        @foreach($consoleUsage as $typeName => $data)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold">{{ $typeName }}</h3>
                            <p class="text-indigo-100 mt-1">Console Type Performance</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-4">
                            <i class="fas fa-gamepad text-3xl"></i>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Total Sessions -->
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-blue-500 rounded-lg p-3 mr-3">
                                <i class="fas fa-play text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Sessions</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $data['sessions'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Revenue -->
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-green-500 rounded-lg p-3 mr-3">
                                <i class="fas fa-dollar-sign text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Revenue</p>
                                <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($data['total_revenue'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Average Session Duration -->
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-purple-500 rounded-lg p-3 mr-3">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Avg Session Duration</p>
                                <p class="text-2xl font-bold text-gray-800">{{ number_format($data['avg_session_duration'], 0) }} min</p>
                            </div>
                        </div>
                    </div>

                    <!-- Average Revenue per Session -->
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-orange-500 rounded-lg p-3 mr-3">
                                <i class="fas fa-chart-line text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Avg Revenue/Session</p>
                                <p class="text-2xl font-bold text-gray-800">Rp {{ $data['sessions'] > 0 ? number_format($data['total_revenue'] / $data['sessions'], 0, ',', '.') : 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if(count($consoleUsage) === 0)
            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-bar text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No Usage Data</h3>
                    <p class="text-gray-600">No completed sessions found for the selected period</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Summary Stats -->
    @php
        $totalSessions = collect($consoleUsage)->sum('sessions');
        $totalRevenue = collect($consoleUsage)->sum('total_revenue');
        $avgDuration = collect($consoleUsage)->avg('avg_session_duration');
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Sessions</p>
                    <p class="text-3xl font-bold mt-2">{{ $totalSessions }}</p>
                </div>
                <i class="fas fa-play text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Revenue</p>
                    <p class="text-2xl font-bold mt-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
                <i class="fas fa-money-bill-wave text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Avg Duration</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($avgDuration, 0) }} min</p>
                </div>
                <i class="fas fa-clock text-4xl opacity-50"></i>
            </div>
        </div>
    </div>
@endsection
