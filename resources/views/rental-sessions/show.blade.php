@extends('layouts.app')

@section('title', 'Rental Session Details')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <a href="{{ route('rental-sessions.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Sessions
                </a>
                <h1 class="text-3xl font-bold">Rental Session #{{ $rentalSession->id }}</h1>
            </div>
            <div class="flex items-center space-x-3">
            <span class="px-4 py-2 rounded-lg text-white font-semibold {{ $rentalSession->status === 'active' ? 'bg-green-600' : ($rentalSession->status === 'paused' ? 'bg-yellow-600' : 'bg-gray-600') }}">
                {{ ucfirst($rentalSession->status) }}
            </span>
                <button onclick="printThermalInvoice()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 font-semibold">
                    <i class="fas fa-print mr-2"></i>Print Invoice
                </button>
            </div>
        </div>

        <!-- Timer Display (Large) -->
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
                        <div class="flex justify-between">
                            <p class="text-gray-500 text-sm">Package Duration</p>
                            <p class="font-medium">{{ $rentalSession->package->duration_minutes }} minutes</p>
                        </div>
                    @endif
                    <div class="flex justify-between border-t pt-2">
                        <p class="text-gray-500 text-sm">Customer</p>
                        <p class="font-medium">{{ $rentalSession->customer_name ?? 'Walk-in Customer' }}</p>
                    </div>
                </div>
            </div>

            <!-- Time Tracking -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-clock text-indigo-600 mr-2"></i>
                    Time Tracking
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
                        <p class="text-gray-500 text-sm">Total Paused Time</p>
                        <p class="font-medium">{{ $rentalSession->total_paused_minutes }} minutes</p>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                        <p class="text-gray-500 text-sm">Elapsed Time</p>
                        <p class="font-medium" id="elapsedTime">
                            @if($rentalSession->end_time)
                                {{ $rentalSession->start_time->diffInMinutes($rentalSession->end_time) - $rentalSession->total_paused_minutes }} minutes
                            @else
                                {{ $rentalSession->start_time->diffInMinutes(now()) - $rentalSession->total_paused_minutes }} minutes
                            @endif
                        </p>
                    </div>
                    @if($currentCost !== null)
                        <div class="flex justify-between border-t pt-2">
                            <p class="text-gray-500 text-sm font-semibold">Current Cost</p>
                            <p class="text-2xl font-bold text-indigo-600">Rp {{ number_format($currentCost, 0, ',', '.') }}</p>
                        </div>
                    @elseif($rentalSession->status === 'completed')
                        <div class="flex justify-between border-t pt-2">
                            <p class="text-gray-500 text-sm font-semibold">Total Cost</p>
                            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($rentalSession->total_cost, 0, ',', '.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Food Orders -->
        @php
            $foodOrders = \App\Models\Order::where('rental_session_id', $rentalSession->id)->with('items.foodItem')->get();
        @endphp
        @if($foodOrders->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-utensils text-orange-600 mr-2"></i>
                    Food & Beverage Orders
                </h2>
                @foreach($foodOrders as $order)
                    <div class="border rounded-lg p-4 mb-4">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-semibold">Order #{{ $order->order_number }}</h3>
                            <span class="text-sm {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-red-600' }} font-medium">
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

        <!-- Actions -->
        @if(in_array($rentalSession->status, ['active', 'paused']))
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Actions</h2>
                <div class="flex flex-wrap gap-3">
                    @if($rentalSession->status === 'active')
                        <form method="POST" action="{{ route('rental-sessions.pause', $rentalSession) }}">
                            @csrf
                            <button type="submit" class="bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-600 font-semibold">
                                <i class="fas fa-pause mr-2"></i>Pause Session
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('rental-sessions.resume', $rentalSession) }}">
                            @csrf
                            <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 font-semibold">
                                <i class="fas fa-play mr-2"></i>Resume Session
                            </button>
                        </form>
                    @endif

                    <button onclick="document.getElementById('extendModal').classList.remove('hidden')" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 font-semibold">
                        <i class="fas fa-clock mr-2"></i>Extend Time
                    </button>

                    <form method="POST" action="{{ route('rental-sessions.end', $rentalSession) }}" onsubmit="return confirm('Are you sure you want to end this session?')">
                        @csrf
                        <button type="submit" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 font-semibold">
                            <i class="fas fa-stop mr-2"></i>End Session
                        </button>
                    </form>

                    <a href="{{ route('orders.create', ['session_id' => $rentalSession->id]) }}" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 font-semibold">
                        <i class="fas fa-shopping-cart mr-2"></i>Add Food Order
                    </a>
                </div>
            </div>
        @endif

        @if($rentalSession->notes)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Notes</h2>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $rentalSession->notes }}</p>
            </div>
        @endif
    </div>

    <!-- Extend Modal -->
    <div id="extendModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 max-w-md w-full">
            <h3 class="text-xl font-semibold mb-4">Extend Session Time</h3>
            <form method="POST" action="{{ route('rental-sessions.extend', $rentalSession) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2 font-medium">Additional Minutes</label>
                    <input type="number" name="additional_minutes" min="1" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-indigo-500" required>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 font-semibold">Extend</button>
                    <button type="button" onclick="document.getElementById('extendModal').classList.add('hidden')" class="flex-1 bg-gray-300 px-6 py-2 rounded-lg hover:bg-gray-400 font-semibold">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Thermal Invoice Template (Hidden) -->
    <div id="thermalInvoice" style="display: none;">
        <div style="width: 300px; font-family: monospace; font-size: 12px;">
            <div style="text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px;">
                <h2 style="margin: 0; font-size: 18px;">PS RENTAL</h2>
                <p style="margin: 2px 0; font-size: 10px;">Gaming Center</p>
                <p style="margin: 2px 0; font-size: 10px;">{{ now()->format('d M Y, H:i:s') }}</p>
            </div>

            <div style="margin-bottom: 10px;">
                <p style="margin: 2px 0;"><strong>Session #{{ $rentalSession->id }}</strong></p>
                <p style="margin: 2px 0;">Customer: {{ $rentalSession->customer_name ?? 'Walk-in' }}</p>
                <p style="margin: 2px 0;">Cashier: {{ $rentalSession->user->name }}</p>
            </div>

            <div style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 10px 0; margin: 10px 0;">
                <p style="margin: 2px 0;"><strong>CONSOLE RENTAL</strong></p>
                <p style="margin: 2px 0;">{{ $rentalSession->console->console_number }} - {{ $rentalSession->console->consoleType->name }}</p>
                <p style="margin: 2px 0;">Start: {{ $rentalSession->start_time->format('H:i') }}</p>
                @if($rentalSession->end_time)
                    <p style="margin: 2px 0;">End: {{ $rentalSession->end_time->format('H:i') }}</p>
                @else
                    <p style="margin: 2px 0;">Status: ONGOING</p>
                @endif
                @if($rentalSession->package)
                    <p style="margin: 2px 0;">Package: {{ $rentalSession->package->name }}</p>
                @endif
                <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                    <span>Console Charges:</span>
                    <span>Rp {{ number_format($currentCost ?? $rentalSession->total_cost, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($foodOrders->count() > 0)
                <div style="margin: 10px 0;">
                    <p style="margin: 2px 0;"><strong>FOOD & BEVERAGE</strong></p>
                    @foreach($foodOrders as $order)
                        @foreach($order->items as $item)
                            <div style="display: flex; justify-content: space-between; margin: 2px 0;">
                                <span>{{ $item->quantity }}x {{ $item->foodItem->name }}</span>
                                <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            @endif

            <div style="border-top: 1px dashed #000; padding-top: 10px; margin-top: 10px;">
                @php
                    $foodTotal = $foodOrders->sum('total');
                    $consoleTotal = $currentCost ?? $rentalSession->total_cost;
                    $subtotal = $consoleTotal + $foodTotal;
                    $tax = $subtotal * 0.10;
                    $grandTotal = $subtotal + $tax;
                @endphp
                <div style="display: flex; justify-content: space-between; margin: 2px 0;">
                    <span>Subtotal:</span>
                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin: 2px 0;">
                    <span>Tax (10%):</span>
                    <span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 16px; font-weight: bold; margin-top: 5px; border-top: 1px solid #000; padding-top: 5px;">
                    <span>TOTAL:</span>
                    <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                </div>
            </div>

            <div style="text-align: center; margin-top: 15px; border-top: 1px dashed #000; padding-top: 10px; font-size: 10px;">
                <p style="margin: 2px 0;">Thank you for your visit!</p>
                <p style="margin: 2px 0;">Please come again</p>
            </div>
        </div>
    </div>

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
                updateElapsedTime(activeSeconds);
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

        function updateElapsedTime(activeSeconds) {
            const elapsedElement = document.getElementById('elapsedTime');
            if (!elapsedElement) return;

            const minutes = Math.floor(activeSeconds / 60);
            const hours = Math.floor(minutes / 60);
            const mins = minutes % 60;

            let timeString = '';
            if (hours > 0) {
                timeString = `${hours} hour${hours > 1 ? 's' : ''} ${mins} minute${mins !== 1 ? 's' : ''}`;
            } else {
                timeString = `${mins} minute${mins !== 1 ? 's' : ''}`;
            }

            elapsedElement.textContent = timeString;
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

        function printThermalInvoice() {
            const invoiceContent = document.getElementById('thermalInvoice').innerHTML;
            const printWindow = window.open('', '', 'width=350,height=600');
            printWindow.document.write('<html><head><title>Print Invoice</title>');
            printWindow.document.write('<style>');
            printWindow.document.write('@page { size: 80mm auto; margin: 0; }');
            printWindow.document.write('body { margin: 10mm; font-family: monospace; font-size: 12px; }');
            printWindow.document.write('</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(invoiceContent);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();

            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
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
