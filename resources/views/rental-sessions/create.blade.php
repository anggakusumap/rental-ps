@extends('layouts.app')

@section('title', 'Start Rental Session')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('rental-sessions.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Sessions
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mt-4">Start New Rental Session</h1>
            <p class="text-gray-600 mt-1">Begin a new gaming session</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <form method="POST" action="{{ route('rental-sessions.store') }}" id="sessionForm">
                @csrf

                <!-- Console Selection -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Console <span class="text-red-500">*</span>
                    </label>
                    <select name="console_id" required class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('console_id') border-red-500 @enderror">
                        <option value="">Select Console</option>
                        @foreach($consoles as $console)
                            <option value="{{ $console->id }}" {{ old('console_id') == $console->id ? 'selected' : '' }}>
                                {{ $console->console_number }} - {{ $console->consoleType->name }}
                                (Rp {{ number_format($console->consoleType->hourly_rate, 0, ',', '.') }}/hour)
                            </option>
                        @endforeach
                    </select>
                    @error('console_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Package Selection -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Package (Optional)
                    </label>
                    <select name="package_id" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                        <option value="">No Package (Hourly Rate)</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                {{ $package->name }} - {{ $package->duration_minutes }} mins - Rp {{ number_format($package->price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-gray-500 text-sm mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Select a package or use hourly rate
                    </p>
                </div>

                <!-- Customer Selection -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Customer (Optional)
                    </label>
                    <select name="customer_id" id="customerSelect" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition mb-3">
                        <option value="">Walk-in Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}{{ $customer->phone ? ' - ' . $customer->phone : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Walk-in Name (Optional)
                    </label>
                    <div id="walkInNameField" class="{{ old('customer_id') ? 'hidden' : '' }}">
                        <input
                            type="text"
                            name="customer_name"
                            id="customerNameInput"
                            value="{{ old('customer_name') }}"
                            placeholder="Enter walk-in customer name (optional)"
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('customer_name') border-red-500 @enderror">
                        @error('customer_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <p class="text-gray-500 text-sm mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Select existing customer or enter walk-in name
                    </p>
                </div>

                <!-- Notes -->
                <div class="mb-8">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Notes (Optional)
                    </label>
                    <textarea
                        name="notes"
                        rows="3"
                        placeholder="Any special notes or requests..."
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">{{ old('notes') }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-green-600 text-white px-6 py-4 rounded-lg hover:bg-green-700 transition font-semibold shadow-lg hover:shadow-xl text-lg">
                        <i class="fas fa-play mr-2"></i>Start Session
                    </button>
                    <a href="{{ route('rental-sessions.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-4 rounded-lg hover:bg-gray-300 transition font-semibold text-center text-lg">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Quick Info -->
        <div class="mt-8 bg-blue-50 border-2 border-blue-200 rounded-xl p-6">
            <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                Quick Guide
            </h3>
            <ul class="space-y-2 text-sm text-blue-800">
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                    <span>Select an available console to begin the session</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                    <span>Choose a package for fixed pricing or use hourly rate</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                    <span>Select customer from database or enter walk-in name</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                    <span>Timer starts automatically when session begins</span>
                </li>
            </ul>
        </div>
    </div>

    <script>
        // Toggle walk-in name field based on customer selection
        document.getElementById('customerSelect').addEventListener('change', function(e) {
            const walkInField = document.getElementById('walkInNameField');
            const nameInput = document.getElementById('customerNameInput');

            if (e.target.value === '') {
                walkInField.classList.remove('hidden');
                nameInput.value = '';
            } else {
                walkInField.classList.add('hidden');
                nameInput.value = '';
            }
        });

        $(document).ready(function () {
            $('#customerSelect').select2({
                placeholder: "Search customer...",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection
