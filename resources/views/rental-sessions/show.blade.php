@extends('layouts.app')

@section('title', 'Rental Session Details')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <a href="{{ route('rental-sessions.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Sessions
                </a>
                <h1 class="text-3xl font-bold">Session #{{ $rentalSession->id }}</h1>
            </div>
            <div class="flex items-center space-x-3">
                @if($rentalSession->invoice && $rentalSession->invoice->payment_status === 'paid')
                    <span class="px-4 py-2 rounded-lg text-white font-semibold bg-green-600">
                        <i class="fas fa-check-circle mr-2"></i>PAID
                    </span>
                @elseif($rentalSession->status === 'completed')
                    <span class="px-4 py-2 rounded-lg text-white font-semibold bg-red-600">
                        <i class="fas fa-exclamation-circle mr-2"></i>UNPAID
                    </span>
                @else
                    <span class="px-4 py-2 rounded-lg text-white font-semibold {{ $rentalSession->status === 'active' ? 'bg-green-600' : ($rentalSession->status === 'paused' ? 'bg-yellow-600' : 'bg-gray-600') }}">
                        {{ ucfirst($rentalSession->status) }}
                    </span>
                @endif

                @if($rentalSession->status === 'completed')
                    <a href="{{ route('rental-sessions.print-receipt', $rentalSession) }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 font-semibold">
                        <i class="fas fa-print mr-2"></i>Print Receipt
                    </a>
                @endif
            </div>
        </div>

        <!-- Timer Display -->
        @if(in_array($rentalSession->status, ['active', 'paused']))
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-2xl p-8 mb-6 text-center">
                <p class="text-white text-lg mb-2 font-medium">{{ $rentalSession->status === 'paused' ? 'PAUSED' : 'TIME REMAINING' }}</p>
                @php
                    $packageMinutes = $rentalSession->package ? $rentalSession->package->duration_minutes : 60;
                    $startTime = $rentalSession->start_time->timestamp;
                    $pausedMinutes = $rentalSession->total_paused_minutes;
                    $currentPausedTime = $rentalSession->status === 'paused' && $rentalSession->paused_at
                        ? now()->diffInMinutes($rentalSession->paused_at)
                        : 0;
                    $totalPausedMinutes = $pausedMinutes + $currentPausedTime;
                @endphp
                <div id="mainTimer"
                     class="text-6xl font-bold text-white font-mono"
                     data-start-time="{{ $startTime }}"
                     data-package-minutes="{{ $packageMinutes }}"
                     data-paused-minutes="{{ $totalPausedMinutes }}"
                     data-status="{{ $rentalSession->status }}"
                     data-session-id="{{ $rentalSession->id }}">
                    --:--:--
                </div>
                <p class="text-indigo-100 mt-2">
                    Package: {{ $rentalSession->package ? $rentalSession->package->name : 'Hourly Rate' }}
                </p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Session Information -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                    Session Information
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <p class="text-gray-500 text-sm">Console</p>
                        <p class="font-medium">{{ $rentalSession->console->console_number }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-500 text-sm">Console Type</p>
                        <p class="font-medium">{{ $rentalSession->console->consoleType->name }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-500 text-sm">Hourly Rate</p>
                        <p class="font-medium">Rp {{ number_format($rentalSession->console->consoleType->hourly_rate, 0, ',', '.') }}</p>
                    </div>
                    @if($rentalSession->package)
                        <div class="flex justify-between">
                            <p class="text-gray-500 text-sm">Package</p>
                            <p class="font-medium">{{ $rentalSession->package->name }}</p>
                        </div>
                    @endif
                    <div class="flex justify-between border-t pt-2">
                        <p class="text-gray-500 text-sm">Customer</p>
                        <p class="font-medium">{{ $rentalSession->customer_name ?? 'Walk-in' }}</p>
                    </div>
                </div>
            </div>

            <!-- Time & Cost Tracking -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-clock text-indigo-600 mr-2"></i>
                    Time & Cost Tracking
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <p class="text-gray-500 text-sm">Start Time</p>
                        <p class="font-medium">{{ $rentalSession->start_time->format('d M Y, H:i') }}</p>
                    </div>
                    @if($rentalSession->end_time)
                        <div class="flex justify-between">
                            <p class="text-gray-500 text-sm">End Time</p>
                            <p class="font-medium">{{ $rentalSession->end_time->format('d M Y, H:i') }}</p>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <p class="text-gray-500 text-sm">Paused Time</p>
                        <p class="font-medium">{{ $rentalSession->total_paused_minutes }} minutes</p>
                    </div>
                    @if($currentCost !== null)
                        <div class="flex justify-between border-t pt-2">
                            <p class="text-gray-500 text-sm font-semibold">Current Cost</p>
                            <p class="text-2xl font-bold text-indigo-600">Rp {{ number_format($currentCost, 0, ',', '.') }}</p>
                        </div>
                    @elseif($rentalSession->status === 'completed')
                        <div class="flex justify-between border-t pt-2">
                            <p class="text-gray-500 text-sm font-semibold">Console Cost</p>
                            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($rentalSession->total_cost, 0, ',', '.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Food Orders -->
        @php
            $foodOrders = \App\Models\Order::where('rental_session_id', $rentalSession->id)->with('items.foodItem')->get();
            $foodTotal = $foodOrders->sum('total');
        @endphp
        @if($foodOrders->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-utensils text-orange-600 mr-2"></i>
                    Food & Beverage Orders
                </h2>
                @foreach($foodOrders as $order)
                    <div class="border-2 rounded-lg p-4 mb-4 {{ $order->payment_status === 'paid' ? 'border-green-200 bg-green-50' : 'border-gray-200' }}">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-semibold">Order #{{ $order->order_number }}</h3>
                            <span class="text-sm {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-red-600' }} font-medium">
                                <i class="fas {{ $order->payment_status === 'paid' ? 'fa-check-circle' : 'fa-exclamation-circle' }} mr-1"></i>
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                        <table class="w-full text-sm">
                            <thead class="border-b">
                            <tr>
                                <th class="text-left py-2">Item</th>
                                <th class="text-center py-2">Qty</th>
                                <th class="text-right py-2">Price</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($order->items as $item)
                                <tr class="border-b">
                                    <td class="py-2">{{ $item->foodItem->name }}</td>
                                    <td class="text-center py-2">{{ $item->quantity }}</td>
                                    <td class="text-right py-2">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr class="font-semibold">
                                <td colspan="2" class="pt-2">Total:</td>
                                <td class="text-right pt-2">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Total Summary (if completed) -->
        @if($rentalSession->status === 'completed')
            <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl shadow-2xl p-8 mb-6 text-white">
                <h2 class="text-2xl font-bold mb-6 text-center">Session Summary</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white bg-opacity-20 rounded-lg p-4 text-center">
                        <i class="fas fa-gamepad text-3xl mb-2"></i>
                        <p class="text-sm opacity-90">Console Charges</p>
                        <p class="text-2xl font-bold">Rp {{ number_format($rentalSession->total_cost, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-4 text-center">
                        <i class="fas fa-utensils text-3xl mb-2"></i>
                        <p class="text-sm opacity-90">F&B Charges</p>
                        <p class="text-2xl font-bold">Rp {{ number_format($foodTotal, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-4 text-center">
                        <i class="fas fa-receipt text-3xl mb-2"></i>
                        <p class="text-sm opacity-90">Total Amount</p>
                        @php
                            $subtotal = $rentalSession->total_cost + $foodTotal;
                            $tax = $subtotal * 0.10;
                            $grandTotal = $subtotal + $tax;
                        @endphp
                        <p class="text-3xl font-bold">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
                        <p class="text-xs opacity-75">(incl. 10% tax)</p>
                    </div>
                </div>

                @if(!$rentalSession->invoice || $rentalSession->invoice->payment_status !== 'paid')
                    <div class="text-center">
                        <button onclick="document.getElementById('paymentModal').classList.remove('hidden')" class="bg-white text-indigo-600 px-8 py-4 rounded-lg hover:bg-gray-100 font-bold text-lg shadow-lg">
                            <i class="fas fa-dollar-sign mr-2"></i>Process Payment
                        </button>
                    </div>
                @endif
            </div>
    @endif

        <!-- Action Buttons -->
        @if(in_array($rentalSession->status, ['active', 'paused']))
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Session Actions</h2>
                <div class="flex flex-wrap gap-3">
                    @if($rentalSession->status === 'active')
                        <form method="POST" action="{{ route('rental-sessions.pause', $rentalSession) }}">
                            @csrf
                            <button type="submit" class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600 font-semibold shadow-lg">
                                <i class="fas fa-pause mr-2"></i>Pause Session
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('rental-sessions.resume', $rentalSession) }}">
                            @csrf
                            <button type="submit" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 font-semibold shadow-lg">
                                <i class="fas fa-play mr-2"></i>Resume Session
                            </button>
                        </form>
                    @endif

                    <button onclick="document.getElementById('extendModal').classList.remove('hidden')" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 font-semibold shadow-lg">
                        <i class="fas fa-clock mr-2"></i>Extend Time
                    </button>

                    <form method="POST" action="{{ route('rental-sessions.end', $rentalSession) }}" onsubmit="return confirm('End this session?')">
                        @csrf
                        <button type="submit" class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 font-semibold shadow-lg">
                            <i class="fas fa-stop mr-2"></i>End Session
                        </button>
                    </form>

                    <a href="{{ route('orders.create', ['session_id' => $rentalSession->id]) }}" class="bg-orange-500 text-white px-6 py-3 rounded-lg hover:bg-orange-600 font-semibold shadow-lg inline-flex items-center">
                        <i class="fas fa-shopping-cart mr-2"></i>Add Food Order
                    </a>
                </div>
            </div>
        @endif

        <!-- Notes -->
        @if($rentalSession->notes)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-sticky-note text-yellow-500 mr-2"></i>
                    Notes
                </h2>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $rentalSession->notes }}</p>
            </div>
        @endif
    </div>

    <!-- Extend Time Modal -->
    <div id="extendModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4">
            <h3 class="text-2xl font-bold mb-4">Extend Session Time</h3>
            <form method="POST" action="{{ route('rental-sessions.extend', $rentalSession) }}">
                @csrf
                <div class="mb-6">
                    <label class="block text-gray-700 mb-2 font-semibold">Additional Minutes</label>
                    <input type="number" name="additional_minutes" min="1" value="30" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500" required>
                    <p class="text-gray-500 text-sm mt-1">How many minutes to add</p>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                        <i class="fas fa-plus mr-2"></i>Extend
                    </button>
                    <button type="button" onclick="document.getElementById('extendModal').classList.add('hidden')" class="flex-1 bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 font-semibold">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment Modal -->
    @if($rentalSession->status === 'completed' && (!$rentalSession->invoice || $rentalSession->invoice->payment_status !== 'paid'))
        <div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4">
                <div class="text-center mb-6">
                    <div class="bg-green-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-dollar-sign text-4xl text-green-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800">Process Payment</h3>
                    <p class="text-gray-600 mt-2">Total Amount</p>
                    <p class="text-4xl font-bold text-green-600 mt-2">
                        Rp {{ number_format($grandTotal ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <form method="POST" action="{{ route('rental-sessions.mark-paid', $rentalSession) }}">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-3">Payment Method</label>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-green-500 transition">
                                <input type="radio" name="payment_method" value="cash" required class="w-5 h-5 text-green-600">
                                <i class="fas fa-money-bill-wave text-green-600 text-2xl mx-3"></i>
                                <span class="font-medium text-gray-800">Cash</span>
                            </label>
                            <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition">
                                <input type="radio" name="payment_method" value="card" class="w-5 h-5 text-blue-600">
                                <i class="fas fa-credit-card text-blue-600 text-2xl mx-3"></i>
                                <span class="font-medium text-gray-800">Card</span>
                            </label>
                            <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-purple-500 transition">
                                <input type="radio" name="payment_method" value="transfer" class="w-5 h-5 text-purple-600">
                                <i class="fas fa-exchange-alt text-purple-600 text-2xl mx-3"></i>
                                <span class="font-medium text-gray-800">Bank Transfer/QRIS</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <button type="submit" class="flex-1 bg-green-600 text-white px-6 py-4 rounded-lg hover:bg-green-700 transition font-bold text-lg shadow-lg">
                            <i class="fas fa-check mr-2"></i>Confirm Payment
                        </button>
                        <button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')" class="flex-1 bg-gray-300 text-gray-700 px-6 py-4 rounded-lg hover:bg-gray-400 transition font-semibold">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Audio -->
    <audio id="sessionAlarm" preload="auto">
        <source src="{{ asset('sounds/alarm.mp3') }}" type="audio/mpeg">
    </audio>

    <script>
        const serverTimeOffset = {{ now()->timestamp }} * 1000 - Date.now();
        let mainTimerInterval = null;

        function getServerTime() {
            return Date.now() + serverTimeOffset;
        }

        function initializeMainTimer() {
            const timerElement = document.getElementById('mainTimer');
            if (!timerElement) return;

            const startTime = parseInt(timerElement.dataset.startTime) * 1000;
            const packageMinutes = parseInt(timerElement.dataset.packageMinutes);
            const pausedMinutes = parseInt(timerElement.dataset.pausedMinutes);
            const status = timerElement.dataset.status;
            const sessionId = timerElement.dataset.sessionId;

            if (status === 'paused') {
                const remainingSeconds = (packageMinutes - pausedMinutes) * 60;
                updateMainTimerDisplay(remainingSeconds, true);
                return;
            }

            mainTimerInterval = setInterval(() => {
                const now = getServerTime();
                const elapsedMs = now - startTime;
                const elapsedSeconds = Math.floor(elapsedMs / 1000);
                const activeSeconds = elapsedSeconds - (pausedMinutes * 60);

                const packageSeconds = packageMinutes * 60;
                const remainingSeconds = packageSeconds - activeSeconds;

                if (remainingSeconds <= 0) {
                    triggerSessionAlarm(sessionId);
                    clearInterval(mainTimerInterval);
                    return;
                }

                updateMainTimerDisplay(remainingSeconds, false);
            }, 1000);
        }

        function updateMainTimerDisplay(totalSeconds, isPaused) {
            const timerElement = document.getElementById('mainTimer');
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            const timeString = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            timerElement.textContent = timeString;

            const totalMinutes = Math.floor(totalSeconds / 60);

            if (isPaused) {
                timerElement.style.color = '#EAB308';
            } else if (totalMinutes <= 5) {
                timerElement.style.color = '#DC2626';
                timerElement.style.animation = 'pulse 1s infinite';
            } else if (totalMinutes <= 15) {
                timerElement.style.color = '#F59E0B';
            } else {
                timerElement.style.color = '#FFFFFF';
            }
        }

        function triggerSessionAlarm(sessionId) {
            const audio = document.getElementById('sessionAlarm');
            audio.loop = true;
            audio.volume = 1.0;
            audio.play();

            const timerElement = document.getElementById('mainTimer');
            timerElement.textContent = '00:00:00';
            timerElement.style.color = '#DC2626';
            timerElement.style.animation = 'pulse 0.5s infinite';

            if (confirm('⏰ TIME\'S UP!\n\nSession #' + sessionId + ' has expired.\n\nDo you want to end this session now?')) {
                audio.pause();
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/rental-sessions/${sessionId}/end`;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                document.body.appendChild(form);
                form.submit();
            } else {
                audio.pause();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            initializeMainTimer();
        });

        window.addEventListener('beforeunload', () => {
            if (mainTimerInterval) {
                clearInterval(mainTimerInterval);
            }
        });
    </script>
@endsection
