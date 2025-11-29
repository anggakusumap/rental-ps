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
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Timer</th>
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
                        <td class="px-6 py-4">
                            @if(in_array($session->status, ['active', 'paused']))
                                @php
                                    $packageMinutes = $session->package ? $session->package->duration_minutes : 60;
                                    $startTime = $session->start_time->timestamp;
                                    $pausedMinutes = $session->total_paused_minutes;
                                    $currentPausedTime = $session->status === 'paused' && $session->paused_at
                                        ? now()->diffInMinutes($session->paused_at)
                                        : 0;
                                    $totalPausedMinutes = $pausedMinutes + $currentPausedTime;
                                @endphp
                                <div class="countdown-timer font-mono text-lg font-bold"
                                     data-session-id="{{ $session->id }}"
                                     data-start-time="{{ $startTime }}"
                                     data-package-minutes="{{ $packageMinutes }}"
                                     data-paused-minutes="{{ $totalPausedMinutes }}"
                                     data-status="{{ $session->status }}"
                                     data-server-time="{{ now()->timestamp }}">
                                    <span class="timer-display text-green-600">--:--:--</span>
                                </div>
                            @else
                                <span class="text-sm text-gray-500">--:--:--</span>
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
                        <td colspan="8" class="px-6 py-12 text-center">
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

    <!-- Alarm Audio (Hidden) -->
    <audio id="alarmSound" preload="auto">
        <source src="{{ asset('sounds/alarm.mp3') }}" type="audio/mpeg">
        <source src="{{ asset('sounds/alarm.ogg') }}" type="audio/ogg">
    </audio>

    <!-- Timer Expired Modal -->
    <div id="timerExpiredModal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4 animate-pulse">
            <div class="text-center">
                <div class="bg-red-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bell text-4xl text-red-600 animate-bounce"></i>
                </div>
                <h3 class="text-2xl font-bold text-red-600 mb-2">TIME'S UP!</h3>
                <p class="text-gray-700 mb-4">Session <span id="expiredSessionId"></span> has expired</p>
                <p class="text-sm text-gray-600 mb-6">Console: <span id="expiredConsole"></span></p>
                <div class="flex space-x-3">
                    <button onclick="stopAlarm()" class="flex-1 bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 font-semibold">
                        <i class="fas fa-stop mr-2"></i>Stop Alarm
                    </button>
                    <button onclick="viewExpiredSession()" class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-semibold">
                        <i class="fas fa-eye mr-2"></i>View Session
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let timers = {};
        let alarmPlaying = false;
        let expiredSessionId = null;
        const serverTimeOffset = {{ now()->timestamp }} * 1000 - Date.now();

        function getServerTime() {
            return Date.now() + serverTimeOffset;
        }

        function initializeTimers() {
            document.querySelectorAll('.countdown-timer').forEach(element => {
                const sessionId = element.dataset.sessionId;
                const startTime = parseInt(element.dataset.startTime) * 1000;
                const packageMinutes = parseInt(element.dataset.packageMinutes);
                const pausedMinutes = parseInt(element.dataset.pausedMinutes);
                const status = element.dataset.status;

                if (status === 'paused') {
                    const remainingMinutes = packageMinutes - pausedMinutes;
                    updateTimerDisplay(element, remainingMinutes * 60, true);
                    return;
                }

                timers[sessionId] = setInterval(() => {
                    updateTimer(sessionId, startTime, packageMinutes, pausedMinutes, element);
                }, 1000);
            });
        }

        function updateTimer(sessionId, startTime, packageMinutes, pausedMinutes, element) {
            const now = getServerTime();
            const elapsedMs = now - startTime;
            const elapsedMinutes = Math.floor(elapsedMs / 60000);
            const elapsedSeconds = Math.floor(elapsedMs / 1000);
            const activeMinutes = elapsedMinutes - pausedMinutes;
            const activeSeconds = elapsedSeconds - (pausedMinutes * 60);

            const packageSeconds = packageMinutes * 60;
            const remainingSeconds = packageSeconds - activeSeconds;

            if (remainingSeconds <= 0) {
                triggerAlarm(sessionId, element);
                clearInterval(timers[sessionId]);
                return;
            }

            updateTimerDisplay(element, remainingSeconds, false);
        }

        function updateTimerDisplay(element, totalSeconds, isPaused) {
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            const display = element.querySelector('.timer-display');
            const timeString = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            display.textContent = timeString;

            const totalMinutes = Math.floor(totalSeconds / 60);

            if (isPaused) {
                display.className = 'timer-display text-yellow-600';
            } else if (totalMinutes <= 5) {
                display.className = 'timer-display text-red-600 font-bold animate-pulse';
            } else if (totalMinutes <= 15) {
                display.className = 'timer-display text-orange-600';
            } else {
                display.className = 'timer-display text-green-600';
            }
        }

        function triggerAlarm(sessionId, element) {
            if (alarmPlaying) return;

            alarmPlaying = true;
            expiredSessionId = sessionId;

            const audio = document.getElementById('alarmSound');
            audio.loop = true;
            audio.volume = 1.0;
            audio.play().catch(e => console.error('Audio play failed:', e));

            const consoleName = element.closest('tr').querySelector('td:nth-child(2) .text-sm').textContent;
            document.getElementById('expiredSessionId').textContent = '#' + sessionId;
            document.getElementById('expiredConsole').textContent = consoleName;
            document.getElementById('timerExpiredModal').classList.remove('hidden');

            element.querySelector('.timer-display').textContent = '00:00:00';
            element.querySelector('.timer-display').className = 'timer-display text-red-600 font-bold animate-pulse';
        }

        function stopAlarm() {
            const audio = document.getElementById('alarmSound');
            audio.pause();
            audio.currentTime = 0;
            audio.loop = false;
            alarmPlaying = false;
            document.getElementById('timerExpiredModal').classList.add('hidden');

            if (confirm('Do you want to end this session now?')) {
                window.location.href = `/rental-sessions/${expiredSessionId}`;
            }
        }

        function viewExpiredSession() {
            stopAlarm();
            window.location.href = `/rental-sessions/${expiredSessionId}`;
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            initializeTimers();
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            Object.keys(timers).forEach(sessionId => {
                clearInterval(timers[sessionId]);
            });
        });
    </script>
@endsection
