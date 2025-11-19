@extends('layouts.app')

@section('title', 'Packages')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Time Packages</h1>
            <p class="text-gray-600 mt-1">Special pricing packages for extended gaming</p>
        </div>
        <a href="{{ route('packages.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition shadow-lg hover:shadow-xl flex items-center">
            <i class="fas fa-plus mr-2"></i>Create Package
        </a>
    </div>
</div>

<!-- Popular Tag -->
<div class="mb-6">
    <div class="inline-flex items-center bg-yellow-100 text-yellow-800 px-4 py-2 rounded-full text-sm font-medium">
        <i class="fas fa-star mr-2"></i>
        Save more with longer packages!
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($packages as $package)
    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group {{ $package->duration_minutes >= 240 ? 'ring-2 ring-indigo-500' : '' }}">
        @if($package->duration_minutes >= 240)
        <!-- Best Value Badge -->
        <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-white text-center py-2 font-bold text-sm">
            <i class="fas fa-crown mr-1"></i>BEST VALUE
        </div>
        @endif

        <!-- Package Header -->
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white p-6">
            <div class="flex items-center justify-between mb-2">
                <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <i class="fas fa-box-open text-2xl"></i>
                </div>
                @if($package->is_active)
                <span class="bg-white bg-opacity-20 text-white text-xs font-semibold px-3 py-1 rounded-full">Active</span>
                @else
                <span class="bg-black bg-opacity-20 text-white text-xs font-semibold px-3 py-1 rounded-full">Inactive</span>
                @endif
            </div>
            <h3 class="text-2xl font-bold mb-1">{{ $package->name }}</h3>
            <p class="text-indigo-100 text-sm">{{ $package->description }}</p>
        </div>

        <div class="p-6">
            <!-- Duration -->
            <div class="flex items-center justify-center mb-4 p-4 bg-gray-50 rounded-xl">
                <div class="text-center">
                    <p class="text-gray-500 text-sm mb-1">Duration</p>
                    <div class="flex items-baseline justify-center">
                        <span class="text-4xl font-bold text-gray-800">{{ floor($package->duration_minutes / 60) }}</span>
                        <span class="text-xl text-gray-600 ml-1">hours</span>
                        @if($package->duration_minutes % 60 > 0)
                        <span class="text-2xl font-bold text-gray-800 ml-2">{{ $package->duration_minutes % 60 }}</span>
                        <span class="text-lg text-gray-600 ml-1">min</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="text-center mb-6">
                <p class="text-gray-500 text-sm mb-2">Package Price</p>
                <div class="flex items-baseline justify-center">
                    <span class="text-sm text-gray-600 mr-1">Rp</span>
                    <span class="text-4xl font-bold text-indigo-600">{{ number_format($package->price, 0, ',', '.') }}</span>
                </div>
                <p class="text-sm text-gray-500 mt-2">
                    ~Rp {{ number_format($package->price / ($package->duration_minutes / 60), 0, ',', '.') }}/hour
                </p>
            </div>

            <!-- Savings Calculator -->
            @php
            $regularHourlyRate = 20000; // Average rate
            $regularCost = ($package->duration_minutes / 60) * $regularHourlyRate;
            $savings = $regularCost - $package->price;
            $savingsPercent = ($savings / $regularCost) * 100;
            @endphp
            @if($savings > 0)
            <div class="bg-green-50 border-2 border-green-200 rounded-xl p-4 mb-4">
                <div class="flex items-center justify-between text-green-800">
                    <div>
                        <p class="text-xs font-medium mb-1">YOU SAVE</p>
                        <p class="text-2xl font-bold">Rp {{ number_format($savings, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-green-200 rounded-full px-3 py-1">
                        <p class="text-sm font-bold">{{ number_format($savingsPercent, 0) }}% OFF</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Features -->
            <div class="space-y-2 mb-6">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span>All console types</span>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span>Pause & resume anytime</span>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span>Extendable with hourly rate</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex space-x-2">
                <a href="{{ route('packages.edit', $package) }}" class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-center font-medium">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <form method="POST" action="{{ route('packages.destroy', $package) }}" class="flex-1" onsubmit="return confirm('Delete this package?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-100 text-red-700 px-4 py-2 rounded-lg hover:bg-red-200 transition font-medium">
                        <i class="fas fa-trash mr-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <div class="bg-purple-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-box text-4xl text-purple-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">No Packages Found</h3>
            <p class="text-gray-600 mb-6">Create pricing packages to offer better deals</p>
            <a href="{{ route('packages.create') }}" class="inline-flex items-center bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i>Create Package
            </a>
        </div>
    </div>
    @endforelse
</div>

@if($packages->hasPages())
<div class="mt-8">
    {{ $packages->links() }}
</div>
@endif
@endsection
