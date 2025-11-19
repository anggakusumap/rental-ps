@extends('layouts.app')

@section('title', 'Console Types')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Console Types</h1>
                <p class="text-gray-600 mt-1">Manage console categories and pricing</p>
            </div>
            <a href="{{ route('console-types.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition shadow-lg hover:shadow-xl flex items-center">
                <i class="fas fa-plus mr-2"></i>Add Console Type
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($consoleTypes as $type)
            <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                <!-- Colored Header -->
                <div class="h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center">
                            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-4 mr-4 shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-gamepad text-3xl text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800">{{ $type->name }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $type->consoles_count }} consoles</p>
                            </div>
                        </div>

                        @if($type->is_active)
                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">Active</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-3 py-1 rounded-full">Inactive</span>
                        @endif
                    </div>

                    @if($type->description)
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $type->description }}</p>
                    @endif

                    <!-- Pricing Card -->
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-4 mb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-600 uppercase font-medium mb-1">Hourly Rate</p>
                                <p class="text-3xl font-bold text-indigo-600">Rp {{ number_format($type->hourly_rate, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-600">Per hour</p>
                                <p class="text-sm font-medium text-gray-700 mt-1">~Rp {{ number_format($type->hourly_rate / 60, 0, ',', '.') }}/min</p>
                            </div>
                        </div>
                    </div>

                    <!-- Example Calculations -->
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500 mb-1">1 Hour</p>
                            <p class="font-bold text-sm text-gray-800">Rp {{ number_format($type->hourly_rate, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500 mb-1">2 Hours</p>
                            <p class="font-bold text-sm text-gray-800">Rp {{ number_format($type->hourly_rate * 2, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500 mb-1">3 Hours</p>
                            <p class="font-bold text-sm text-gray-800">Rp {{ number_format($type->hourly_rate * 3, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <a href="{{ route('console-types.edit', $type) }}" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-center font-medium">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        @if($type->consoles_count == 0)
                            <form method="POST" action="{{ route('console-types.destroy', $type) }}" class="flex-1" onsubmit="return confirm('Delete this console type?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition font-medium">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                        @else
                            <button disabled class="flex-1 bg-gray-300 text-gray-500 px-4 py-2 rounded-lg cursor-not-allowed font-medium" title="Cannot delete - has consoles">
                                <i class="fas fa-lock mr-1"></i>Delete
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <div class="bg-indigo-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-list text-4xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No Console Types Found</h3>
                    <p class="text-gray-600 mb-6">Create your first console type to get started</p>
                    <a href="{{ route('console-types.create') }}" class="inline-flex items-center bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i>Add Console Type
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($consoleTypes->hasPages())
        <div class="mt-8">
            {{ $consoleTypes->links() }}
        </div>
    @endif
@endsection
