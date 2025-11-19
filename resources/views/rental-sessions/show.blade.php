@extends('layouts.app')

@section('title', 'Rental Session Details')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Rental Session #{{ $rentalSession->id }}</h1>
            <span class="px-4 py-2 rounded text-white {{ $rentalSession->status === 'active' ? 'bg-green-600' : ($rentalSession->status === 'paused' ? 'bg-yellow-600' : 'bg-gray-600') }}">
            {{ ucfirst($rentalSession->status) }}
        </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Session Information</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-gray-500 text-sm">Console</p>
                        <p class="font-medium">{{ $rentalSession->console->console_number }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Console Type</p>
                        <p class="font-medium">{{ $rentalSession->console->consoleType->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Hourly Rate</p>
                        <p class="font-medium">Rp {{ number_format($rentalSession->console->consoleType->hourly_rate, 0, ',', '.') }}</p>
                    </div>
                    @if($rentalSession->package)
                        <div>
                            <p class="text-gray-500 text-sm">Package</p>
                            <p class="font-medium">{{ $rentalSession->package->name }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-gray-500 text-sm">Customer</p>
                        <p class="font-medium">{{ $rentalSession->customer_name ?? 'Walk-in Customer' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Time Tracking</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-gray-500 text-sm">Start Time</p>
                        <p class="font-medium">{{ $rentalSession->start_time->format('d M Y, H:i') }}</p>
                    </div>
                    @if($rentalSession->end_time)
                        <div>
                            <p class="text-gray-500 text-sm">End Time</p>
                            <p class="font-medium">{{ $rentalSession->end_time->format('d M Y, H:i') }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-gray-500 text-sm">Total Paused Time</p>
                        <p class="font-medium">{{ $rentalSession->total_paused_minutes }} minutes</p>
                    </div>
                    @if($currentCost)
                        <div class="border-t pt-3">
                            <p class="text-gray-500 text-sm">Current Cost</p>
                            <p class="text-2xl font-bold text-indigo-600">Rp {{ number_format($currentCost, 0, ',', '.') }}</p>
                        </div>
                    @elseif($rentalSession->status === 'completed')
                        <div class="border-t pt-3">
                            <p class="text-gray-500 text-sm">Total Cost</p>
                            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($rentalSession->total_cost, 0, ',', '.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(in_array($rentalSession->status, ['active', 'paused']))
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Actions</h2>
                <div class="flex flex-wrap gap-3">
                    @if($rentalSession->status === 'active')
                        <form method="POST" action="{{ route('rental-sessions.pause', $rentalSession) }}">
                            @csrf
                            <button type="submit" class="bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600">
                                <i class="fas fa-pause mr-2"></i>Pause Session
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('rental-sessions.resume', $rentalSession) }}">
                            @csrf
                            <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">
                                <i class="fas fa-play mr-2"></i>Resume Session
                            </button>
                        </form>
                    @endif

                    <button onclick="document.getElementById('extendModal').classList.remove('hidden')" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                        <i class="fas fa-clock mr-2"></i>Extend Time
                    </button>

                    <form method="POST" action="{{ route('rental-sessions.end', $rentalSession) }}" onsubmit="return confirm('Are you sure you want to end this session?')">
                        @csrf
                        <button type="submit" class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600">
                            <i class="fas fa-stop mr-2"></i>End Session
                        </button>
                    </form>

                    <a href="{{ route('orders.create', ['session_id' => $rentalSession->id]) }}" class="bg-indigo-500 text-white px-6 py-2 rounded hover:bg-indigo-600">
                        <i class="fas fa-shopping-cart mr-2"></i>Add Food Order
                    </a>
                </div>
            </div>
        @endif

        @if($rentalSession->notes)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Notes</h2>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $rentalSession->notes }}</p>
            </div>
        @endif
    </div>

    <!-- Extend Modal -->
    <div id="extendModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <h3 class="text-xl font-semibold mb-4">Extend Session Time</h3>
            <form method="POST" action="{{ route('rental-sessions.extend', $rentalSession) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Additional Minutes</label>
                    <input type="number" name="additional_minutes" min="1" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">Extend</button>
                    <button type="button" onclick="document.getElementById('extendModal').classList.add('hidden')" class="bg-gray-300 px-6 py-2 rounded hover:bg-gray-400">Cancel</button>
                </div>
            </form>
        </div>
    </div>
@endsection
