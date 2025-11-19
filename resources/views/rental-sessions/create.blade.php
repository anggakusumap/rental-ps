@extends('layouts.app')

@section('title', 'Start Rental Session')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Start New Rental Session</h1>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('rental-sessions.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Console</label>
                    <select name="console_id" required class="w-full border rounded px-3 py-2">
                        <option value="">Select Console</option>
                        @foreach($consoles as $console)
                            <option value="{{ $console->id }}">
                                {{ $console->console_number }} - {{ $console->consoleType->name }} (Rp {{ number_format($console->consoleType->hourly_rate, 0, ',', '.') }}/hour)
                            </option>
                        @endforeach
                    </select>
                    @error('console_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Package (Optional)</label>
                    <select name="package_id" class="w-full border rounded px-3 py-2">
                        <option value="">No Package (Hourly Rate)</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}">
                                {{ $package->name }} - {{ $package->duration_minutes }} mins - Rp {{ number_format($package->price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Customer Name (Optional)</label>
                    <input type="text" name="customer_name" class="w-full border rounded px-3 py-2" placeholder="Enter customer name">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full border rounded px-3 py-2" placeholder="Any special notes"></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                        <i class="fas fa-play mr-2"></i>Start Session
                    </button>
                    <a href="{{ route('rental-sessions.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
