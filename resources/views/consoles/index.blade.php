@extends('layouts.app')

@section('title', 'Consoles')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Console Management</h1>
                <p class="text-gray-600 mt-1">Manage all gaming consoles</p>
            </div>
            <a href="{{ route('consoles.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition shadow-lg hover:shadow-xl flex items-center">
                <i class="fas fa-plus mr-2"></i>Add Console
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Available</p>
                    <p class="text-3xl font-bold mt-2">{{ $consoles->where('status', 'available')->count() }}</p>
                </div>
                <i class="fas fa-check-circle text-4xl text-white opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Occupied</p>
                    <p class="text-3xl font-bold mt-2">{{ $consoles->where('status', 'occupied')->count() }}</p>
                </div>
                <i class="fas fa-play-circle text-4xl text-white opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Maintenance</p>
                    <p class="text-3xl font-bold mt-2">{{ $consoles->where('status', 'maintenance')->count() }}</p>
                </div>
                <i class="fas fa-tools text-4xl text-white opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Consoles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($consoles as $console)
            <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                <!-- Status Badge -->
                <div class="relative h-2">
                    @if($console->status === 'available')
                        <div class="absolute inset-0 bg-gradient-to-r from-green-400 to-green-600"></div>
                    @elseif($console->status === 'occupied')
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-blue-600"></div>
                    @else
                        <div class="absolute inset-0 bg-gradient-to-r from-yellow-400 to-yellow-600"></div>
                    @endif
                </div>

                <div class="p-6">
                    <!-- Console Icon -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-4 shadow-lg group-hover:scale-110 transition-transform">
                            <i class="fas fa-gamepad text-3xl text-white"></i>
                        </div>
                        @if($console->status === 'available')
                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">Available</span>
                        @elseif($console->status === 'occupied')
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full flex items-center">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-2 animate-pulse"></span>
                        In Use
                    </span>
                        @else
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full">Maintenance</span>
                        @endif
                    </div>

                    <!-- Console Info -->
                    <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $console->console_number }}</h3>
                    <p class="text-gray-600 text-sm mb-4">{{ $console->consoleType->name }}</p>

                    <!-- Pricing -->
                    <div class="flex items-center text-indigo-600 font-semibold mb-4">
                        <i class="fas fa-tag mr-2"></i>
                        <span>Rp {{ number_format($console->consoleType->hourly_rate, 0, ',', '.') }}/hour</span>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <a href="{{ route('consoles.edit', $console) }}" class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-center text-sm font-medium">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        @if($console->status !== 'occupied')
                            <form method="POST" action="{{ route('consoles.destroy', $console) }}" class="flex-1" onsubmit="return confirm('Delete this console?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-100 text-red-700 px-4 py-2 rounded-lg hover:bg-red-200 transition text-sm font-medium">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                        @endif
                    </div>

                    @if($console->notes)
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-600">{{ $console->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-gamepad text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No Consoles Found</h3>
                    <p class="text-gray-600 mb-6">Add your first console to get started</p>
                    <a href="{{ route('consoles.create') }}" class="inline-flex items-center bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i>Add Console
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($consoles->hasPages())
        <div class="mt-8">
            {{ $consoles->links() }}
        </div>
    @endif
@endsection
