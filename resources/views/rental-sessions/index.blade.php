@extends('layouts.app')

@section('title', 'Rental Sessions')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Rental Sessions</h1>
                <p class="text-gray-600 mt-1">Manage all console rental sessions</p>
            </div>
            <a href="{{ route('rental-sessions.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition shadow-lg hover:shadow-xl flex items-center">
                <i class="fas fa-plus mr-2"></i>Start New Session
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Active Sessions</p>
                    <p class="text-3xl font-bold mt-2">{{ $sessions->where('status', 'active')->count() }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-play text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Paused</p>
                    <p class="text-3xl font-bold mt-2">{{ $sessions->where('status', 'paused')->count() }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-pause text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Completed</p>
                    <p class="text-3xl font-bold mt-2">{{ $sessions->where('status', 'completed')->count() }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Sessions</p>
                    <p class="text-3xl font-bold mt-2">{{ $sessions->total() }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-gamepad text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Sessions Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Session ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Console</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Package</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Start Time</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Duration</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cost</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($sessions as $session)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">#{{ $session->id }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-gamepad text-indigo-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $session->console->console_number }}</p>
                                    <p class="text-xs text-gray-500">{{ $session->console->consoleType->name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $session->customer_name ?? 'Walk-in' }}</div>
                            <div class="text-xs text-gray-500">by {{ $session->user->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($session->package)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $session->package->name }}
                            </span>
                            @else
                                <span class="text-sm text-gray-500">Hourly</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $session->start_time->format('M d, H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            @if($session->end_time)
                                {{ $session->start_time->diffInMinutes($session->end_time) }} min
                            @else
                                {{ $session->start_time->diffInMinutes(now()) }} min
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($session->status === 'active')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                Active
                            </span>
                            @elseif($session->status === 'paused')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                <i class="fas fa-pause mr-1"></i>
                                Paused
                            </span>
                            @elseif($session->status === 'completed')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                <i class="fas fa-check mr-1"></i>
                                Completed
                            </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                {{ ucfirst($session->status) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                            @if($session->status === 'completed')
                                Rp {{ number_format($session->total_cost, 0, ',', '.') }}
                            @else
                                <span class="text-gray-500">Ongoing</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <a href="{{ route('rental-sessions.show', $session) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                View Details →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gray-100 rounded-full p-6 mb-4">
                                    <i class="fas fa-gamepad text-4xl text-gray-400"></i>
                                </div>
                                <p class="text-gray-500 text-lg font-medium">No rental sessions found</p>
                                <p class="text-gray-400 text-sm mt-1">Start a new session to get started</p>
                                <a href="{{ route('rental-sessions.create') }}" class="mt-4 bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                                    Start New Session
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($sessions->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>
@endsection
