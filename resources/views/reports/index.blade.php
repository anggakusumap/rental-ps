@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
    <div class="mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Reports & Analytics</h1>
            <p class="text-gray-600 mt-1">Track your business performance and insights</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        @php
            $todayRevenue = \App\Models\Invoice::whereDate('created_at', today())->where('payment_status', 'paid')->sum('total');
            $monthRevenue = \App\Models\Invoice::whereMonth('created_at', now()->month)->where('payment_status', 'paid')->sum('total');
            $totalSessions = \App\Models\RentalSession::count();
            $activeConsoles = \App\Models\Console::where('status', 'occupied')->count();
        @endphp

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Today's Revenue</p>
                    <p class="text-2xl font-bold mt-2">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
                </div>
                <i class="fas fa-dollar-sign text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">This Month</p>
                    <p class="text-2xl font-bold mt-2">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</p>
                </div>
                <i class="fas fa-chart-line text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Sessions</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($totalSessions) }}</p>
                </div>
                <i class="fas fa-play text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Active Now</p>
                    <p class="text-3xl font-bold mt-2">{{ $activeConsoles }}</p>
                </div>
                <i class="fas fa-gamepad text-4xl opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Revenue Report -->
        <a href="{{ route('reports.revenue') }}" class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 group">
            <div class="flex items-start justify-between mb-4">
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-chart-line text-3xl text-white"></i>
                </div>
                <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">Financial</span>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Revenue Report</h3>
            <p class="text-gray-600 text-sm mb-4">Track daily and monthly revenue with detailed breakdowns</p>
            <div class="flex items-center text-indigo-600 font-medium">
                <span>View Report</span>
                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
            </div>
        </a>

        <!-- Usage Report -->
        <a href="{{ route('reports.usage') }}" class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 group">
            <div class="flex items-start justify-between mb-4">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-chart-bar text-3xl text-white"></i>
                </div>
                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">Operations</span>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Usage Statistics</h3>
            <p class="text-gray-600 text-sm mb-4">Console utilization and session analytics</p>
            <div class="flex items-center text-indigo-600 font-medium">
                <span>View Report</span>
                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
            </div>
        </a>

        <!-- Top Items Report -->
        <a href="{{ route('reports.top-items') }}" class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 group">
            <div class="flex items-start justify-between mb-4">
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-star text-3xl text-white"></i>
                </div>
                <span class="bg-orange-100 text-orange-800 text-xs font-semibold px-3 py-1 rounded-full">F&B</span>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Top Selling Items</h3>
            <p class="text-gray-600 text-sm mb-4">Best performing food and beverage items</p>
            <div class="flex items-center text-indigo-600 font-medium">
                <span>View Report</span>
                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
            </div>
        </a>

        <!-- Export Options -->
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-start justify-between mb-4">
                <div class="bg-white bg-opacity-20 rounded-xl p-4">
                    <i class="fas fa-file-export text-3xl text-white"></i>
                </div>
            </div>
            <h3 class="text-xl font-bold mb-2">Export Data</h3>
            <p class="text-indigo-100 text-sm mb-4">Download reports in CSV format for analysis</p>
            <div class="space-y-2">
                <a href="{{ route('reports.export', ['type' => 'revenue']) }}" class="block bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg px-4 py-2 text-center transition">
                    <i class="fas fa-download mr-2"></i>Revenue Report
                </a>
                <a href="{{ route('reports.export', ['type' => 'sessions']) }}" class="block bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg px-4 py-2 text-center transition">
                    <i class="fas fa-download mr-2"></i>Sessions Report
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-dashed border-gray-300">
            <div class="text-center">
                <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-cog text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Custom Reports</h3>
                <p class="text-gray-600 text-sm mb-4">Need specific analytics? Contact admin for custom report generation.</p>
                <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                    Request Custom Report
                </button>
            </div>
        </div>

        <!-- Performance Insights -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                Quick Insights
            </h3>
            <div class="space-y-3">
                @php
                    $avgSessionTime = \App\Models\RentalSession::where('status', 'completed')
                        ->whereNotNull('end_time')
                        ->get()
                        ->avg(function($session) {
                            return $session->end_time->diffInMinutes($session->start_time);
                        });
                @endphp
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <span class="text-sm text-gray-700">Avg. Session Time</span>
                    <span class="font-bold text-blue-600">{{ number_format($avgSessionTime ?? 0, 0) }} min</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <span class="text-sm text-gray-700">Active Consoles</span>
                    <span class="font-bold text-green-600">{{ $activeConsoles }}/{{ \App\Models\Console::count() }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                    <span class="text-sm text-gray-700">Occupancy Rate</span>
                    <span class="font-bold text-purple-600">{{ \App\Models\Console::count() > 0 ? number_format(($activeConsoles / \App\Models\Console::count()) * 100, 0) : 0 }}%</span>
                </div>
            </div>
        </div>
    </div>
@endsection
