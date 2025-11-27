@extends('layouts.app')

@section('title', 'Edit Console')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('consoles.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Consoles
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mt-4">Edit Console: {{ $console->console_number }}</h1>
            <p class="text-gray-600 mt-1">Update console information</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <form method="POST" action="{{ route('consoles.update', $console) }}">
                @csrf
                @method('PUT')

                <!-- Console Type -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Console Type <span class="text-red-500">*</span>
                    </label>
                    <select name="console_type_id" required class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('console_type_id') border-red-500 @enderror">
                        <option value="">Select Console Type</option>
                        @foreach($consoleTypes as $type)
                            <option value="{{ $type->id }}" {{ (old('console_type_id', $console->console_type_id) == $type->id) ? 'selected' : '' }}>
                                {{ $type->name }} - Rp {{ number_format($type->hourly_rate, 0, ',', '.') }}/hour
                            </option>
                        @endforeach
                    </select>
                    @error('console_type_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Console Number -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Console Number <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="console_number"
                        value="{{ old('console_number', $console->console_number) }}"
                        required
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('console_number') border-red-500 @enderror">
                    @error('console_number')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Console Status
                    </label>
                    <div class="grid grid-cols-3 gap-4">
                        <label class="relative flex items-center justify-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                            <input type="radio" name="status" value="available" {{ old('status', $console->status) == 'available' ? 'checked' : '' }} class="sr-only peer">
                            <div class="text-center peer-checked:text-indigo-600">
                                <i class="fas fa-check-circle text-2xl mb-2"></i>
                                <p class="font-medium">Available</p>
                            </div>
                            <div class="absolute inset-0 border-2 border-indigo-500 rounded-lg opacity-0 peer-checked:opacity-100 transition"></div>
                        </label>

                        <label class="relative flex items-center justify-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500 transition {{ $console->status == 'occupied' ? 'cursor-not-allowed opacity-50' : '' }}">
                            <input type="radio" name="status" value="maintenance" {{ old('status', $console->status) == 'maintenance' ? 'checked' : '' }} class="sr-only peer">
                            <div class="text-center peer-checked:text-indigo-600">
                                <i class="fas fa-tools text-2xl mb-2"></i>
                                <p class="font-medium">Maintenance</p>
                            </div>
                            <div class="absolute inset-0 border-2 border-indigo-500 rounded-lg opacity-0 peer-checked:opacity-100 transition"></div>
                        </label>

                        <label class="relative flex items-center justify-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                            <input type="radio" name="status" value="occupied" {{ old('status', $console->status) == 'occupied' ? 'checked' : '' }} class="sr-only peer">
                            <div class="text-center peer-checked:text-indigo-600">
                                <i class="fas fa-play-circle text-2xl mb-2"></i>
                                <p class="font-medium">Occupied</p>
                            </div>
                            <div class="absolute inset-0 border-2 border-indigo-500 rounded-lg opacity-0 peer-checked:opacity-100 transition"></div>
                        </label>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-8">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Notes (Optional)
                    </label>
                    <textarea
                        name="notes"
                        rows="4"
                        placeholder="Any additional information about this console..."
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">{{ old('notes', $console->notes) }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>Update Console
                    </button>
                    <a href="{{ route('consoles.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold text-center">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Current Session Info (if occupied) -->
        @if($console->status === 'occupied' && $console->currentSession())
            <div class="mt-6 bg-blue-50 border-2 border-blue-200 rounded-xl p-6">
                <div class="flex items-center mb-3">
                    <i class="fas fa-info-circle text-blue-600 text-xl mr-2"></i>
                    <h3 class="text-lg font-bold text-blue-900">Current Session Active</h3>
                </div>
                @php $session = $console->currentSession(); @endphp
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-blue-600 font-medium">Customer</p>
                        <p class="text-blue-900">{{ $session->customer_name ?? 'Walk-in' }}</p>
                    </div>
                    <div>
                        <p class="text-blue-600 font-medium">Started</p>
                        <p class="text-blue-900">{{ $session->start_time->format('M d, H:i') }}</p>
                    </div>
                </div>
                <a href="{{ route('rental-sessions.show', $session) }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800 font-medium">
                    View Session Details →
                </a>
            </div>
        @endif
    </div>
@endsection
