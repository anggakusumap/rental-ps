@extends('layouts.app')

@section('title', 'Edit Package')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('packages.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Packages
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mt-4">Edit Package: {{ $package->name }}</h1>
            <p class="text-gray-600 mt-1">Update package information</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <form method="POST" action="{{ route('packages.update', $package) }}">
                @csrf
                @method('PUT')

                <!-- Package Name -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Package Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $package->name) }}"
                        required
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('name') border-red-500 @enderror">
                    @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Duration -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Duration <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Hours</label>
                            <input
                                type="number"
                                id="hours"
                                value="{{ floor(old('duration_minutes', $package->duration_minutes) / 60) }}"
                                min="0"
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Minutes</label>
                            <input
                                type="number"
                                id="minutes"
                                value="{{ old('duration_minutes', $package->duration_minutes) % 60 }}"
                                min="0"
                                max="59"
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                        </div>
                    </div>
                    <input type="hidden" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $package->duration_minutes) }}">
                    <p class="text-gray-500 text-sm mt-2">
                        <span class="font-semibold">Total:</span> <span id="total-duration">{{ $package->duration_minutes }} minutes</span>
                    </p>
                    @error('duration_minutes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quick Duration Buttons -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Quick Select</label>
                    <div class="grid grid-cols-4 gap-2">
                        <button type="button" onclick="setDuration(60)" class="bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 px-4 py-2 rounded-lg transition font-medium">1 Hour</button>
                        <button type="button" onclick="setDuration(120)" class="bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 px-4 py-2 rounded-lg transition font-medium">2 Hours</button>
                        <button type="button" onclick="setDuration(180)" class="bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 px-4 py-2 rounded-lg transition font-medium">3 Hours</button>
                        <button type="button" onclick="setDuration(240)" class="bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 px-4 py-2 rounded-lg transition font-medium">4 Hours</button>
                    </div>
                </div>

                <!-- Package Price -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Package Price (Rp) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 font-medium">Rp</span>
                        </div>
                        <input
                            type="number"
                            name="price"
                            id="price"
                            value="{{ old('price', $package->price) }}"
                            required
                            min="0"
                            step="1000"
                            class="w-full border-2 border-gray-300 rounded-lg pl-14 pr-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('price') border-red-500 @enderror">
                    </div>
                    @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">
                        <span class="font-semibold">Effective rate:</span> <span id="effective-rate">Rp {{ number_format($package->price / ($package->duration_minutes / 60), 0, ',', '.') }}/hour</span>
                    </p>
                </div>

                <!-- Savings Calculator -->
                <div class="mb-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-calculator text-green-600 mr-2"></i>
                        Savings Calculator
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-white rounded-lg p-3">
                            <p class="text-gray-600 mb-1">Regular Price (@ Rp 20,000/hr)</p>
                            <p class="font-bold text-gray-800" id="regular-price">Rp 0</p>
                        </div>
                        <div class="bg-white rounded-lg p-3">
                            <p class="text-gray-600 mb-1">Package Savings</p>
                            <p class="font-bold text-green-600" id="package-savings">Rp 0 (0%)</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Description (Optional)
                    </label>
                    <textarea
                        name="description"
                        rows="3"
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">{{ old('description', $package->description) }}</textarea>
                </div>

                <!-- Active Status -->
                <div class="mb-8">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 border-2 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-3 text-gray-700 font-medium">Active (available for booking)</span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>Update Package
                    </button>
                    <a href="{{ route('packages.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold text-center">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function setDuration(minutes) {
            const hours = Math.floor(minutes / 60);
            const mins = minutes % 60;
            document.getElementById('hours').value = hours;
            document.getElementById('minutes').value = mins;
            updateDuration();
        }

        function updateDuration() {
            const hours = parseInt(document.getElementById('hours').value) || 0;
            const minutes = parseInt(document.getElementById('minutes').value) || 0;
            const totalMinutes = (hours * 60) + minutes;

            document.getElementById('duration_minutes').value = totalMinutes;
            document.getElementById('total-duration').textContent = totalMinutes + ' minutes (' + hours + 'h ' + minutes + 'm)';

            calculateSavings();
        }

        function calculateSavings() {
            const totalMinutes = parseInt(document.getElementById('duration_minutes').value) || 0;
            const price = parseFloat(document.getElementById('price').value) || 0;
            const hours = totalMinutes / 60;

            if (hours > 0 && price > 0) {
                const effectiveRate = price / hours;
                document.getElementById('effective-rate').textContent = 'Rp ' + Math.round(effectiveRate).toLocaleString('id-ID') + '/hour';

                const regularPrice = hours * 20000;
                const savings = regularPrice - price;
                const savingsPercent = (savings / regularPrice) * 100;

                document.getElementById('regular-price').textContent = 'Rp ' + regularPrice.toLocaleString('id-ID');

                if (savings > 0) {
                    document.getElementById('package-savings').textContent = 'Rp ' + savings.toLocaleString('id-ID') + ' (' + Math.round(savingsPercent) + '% off)';
                    document.getElementById('package-savings').className = 'font-bold text-green-600';
                } else {
                    document.getElementById('package-savings').textContent = 'No savings';
                    document.getElementById('package-savings').className = 'font-bold text-gray-600';
                }
            }
        }

        document.getElementById('hours').addEventListener('input', updateDuration);
        document.getElementById('minutes').addEventListener('input', updateDuration);
        document.getElementById('price').addEventListener('input', calculateSavings);

        // Initialize on load
        window.addEventListener('load', function() {
            updateDuration();
            calculateSavings();
        });
    </script>
@endsection
