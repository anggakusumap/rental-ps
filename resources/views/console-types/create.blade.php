@extends('layouts.app')

@section('title', 'Create Console Type')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('console-types.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Console Types
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mt-4">Create New Console Type</h1>
            <p class="text-gray-600 mt-1">Define a new console category with pricing</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <form method="POST" action="{{ route('console-types.store') }}">
                @csrf

                <!-- Console Type Name -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Console Type Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        placeholder="e.g., PlayStation 5, Xbox Series X"
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
                            value="{{ old('hourly_rate') }}"
                            required
                            min="0"
                            step="1000"
                            placeholder="20000"
                            class="w-full border-2 border-gray-300 rounded-lg pl-14 pr-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('hourly_rate') border-red-500 @enderror">
                    </div>
                    @error('hourly_rate')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        This is the base rate charged per hour
                    </p>
                </div>

                <!-- Rate Preview -->
                <div class="mb-6 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Pricing Preview</h3>
                    <div class="grid grid-cols-4 gap-3">
                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500 mb-1">15 min</p>
                            <p class="font-bold text-sm text-indigo-600" id="preview-15">Rp 0</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500 mb-1">30 min</p>
                            <p class="font-bold text-sm text-indigo-600" id="preview-30">Rp 0</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500 mb-1">1 hour</p>
                            <p class="font-bold text-sm text-indigo-600" id="preview-60">Rp 0</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500 mb-1">2 hours</p>
                            <p class="font-bold text-sm text-indigo-600" id="preview-120">Rp 0</p>
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
                        placeholder="Describe the features or specifications of this console type..."
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">{{ old('description') }}</textarea>
                </div>

                <!-- Active Status -->
                <div class="mb-8">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 border-2 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-3 text-gray-700 font-medium">Active (available for use)</span>
                    </label>
                    <p class="text-gray-500 text-sm mt-1 ml-8">
                        Inactive console types won't appear in the rental session creation
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>Create Console Type
                    </button>
                    <a href="{{ route('console-types.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold text-center">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
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
