@extends('layouts.app')

@section('title', 'Edit Console Type')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('console-types.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Console Types
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mt-4">Edit Console Type: {{ $consoleType->name }}</h1>
            <p class="text-gray-600 mt-1">Update console type information</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <form method="POST" action="{{ route('console-types.update', $consoleType) }}">
                @csrf
                @method('PUT')

                <!-- Console Type Name -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Console Type Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $consoleType->name) }}"
                        required
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('name') border-red-500 @enderror">
                    @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hourly Rate -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Hourly Rate (Rp) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 font-medium">Rp</span>
                        </div>
                        <input
                            type="number"
                            name="hourly_rate"
                            value="{{ old('hourly_rate', $consoleType->hourly_rate) }}"
                            required
                            min="0"
                            step="1000"
                            class="w-full border-2 border-gray-300 rounded-lg pl-14 pr-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('hourly_rate') border-red-500 @enderror">
                    </div>
                    @error('hourly_rate')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rate Preview -->
                <div class="mb-6 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Pricing Preview</h3>
                    <div class="grid grid-cols-4 gap-3">
                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500 mb-1">15 min</p>
                            <p class="font-bold text-sm text-indigo-600" id="preview-15">Rp {{ number_format($consoleType->hourly_rate / 4, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500 mb-1">30 min</p>
                            <p class="font-bold text-sm text-indigo-600" id="preview-30">Rp {{ number_format($consoleType->hourly_rate / 2, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500 mb-1">1 hour</p>
                            <p class="font-bold text-sm text-indigo-600" id="preview-60">Rp {{ number_format($consoleType->hourly_rate, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500 mb-1">2 hours</p>
                            <p class="font-bold text-sm text-indigo-600" id="preview-120">Rp {{ number_format($consoleType->hourly_rate * 2, 0, ',', '.') }}</p>
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
                        rows="4"
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">{{ old('description', $consoleType->description) }}</textarea>
                </div>

                <!-- Active Status -->
                <div class="mb-8">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $consoleType->is_active) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 border-2 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-3 text-gray-700 font-medium">Active (available for use)</span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>Update Console Type
                    </button>
                    <a href="{{ route('console-types.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold text-center">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Consoles Count -->
        @if($consoleType->consoles()->count() > 0)
            <div class="mt-6 bg-blue-50 border-2 border-blue-200 rounded-xl p-6">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-600 text-xl mr-3"></i>
                    <div>
                        <p class="font-semibold text-blue-900">{{ $consoleType->consoles()->count() }} console(s) using this type</p>
                        <p class="text-sm text-blue-700 mt-1">Changing the hourly rate will affect future rental sessions for all consoles of this type.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        document.querySelector('input[name="hourly_rate"]').addEventListener('input', function(e) {
            const rate = parseFloat(e.target.value) || 0;
            document.getElementById('preview-15').textContent = 'Rp ' + Math.round(rate / 4).toLocaleString('id-ID');
            document.getElementById('preview-30').textContent = 'Rp ' + Math.round(rate / 2).toLocaleString('id-ID');
            document.getElementById('preview-60').textContent = 'Rp ' + rate.toLocaleString('id-ID');
            document.getElementById('preview-120').textContent = 'Rp ' + (rate * 2).toLocaleString('id-ID');
        });
    </script>
@endsection
