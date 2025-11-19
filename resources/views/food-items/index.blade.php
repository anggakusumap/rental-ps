@extends('layouts.app')

@section('title', 'Food & Beverage Menu')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Food & Beverage Menu</h1>
                <p class="text-gray-600 mt-1">Manage your F&B catalog and inventory</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('food-categories.index') }}" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-folder mr-2"></i>Categories
                </a>
                <a href="{{ route('food-items.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition shadow-lg hover:shadow-xl flex items-center">
                    <i class="fas fa-plus mr-2"></i>Add Item
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Items</p>
                    <p class="text-3xl font-bold mt-2">{{ $foodItems->total() }}</p>
                </div>
                <i class="fas fa-utensils text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">In Stock</p>
                    <p class="text-3xl font-bold mt-2">{{ $foodItems->where('stock', '>', 0)->count() }}</p>
                </div>
                <i class="fas fa-check-circle text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Low Stock</p>
                    <p class="text-3xl font-bold mt-2">{{ $foodItems->where('stock', '>', 0)->where('stock', '<', 10)->count() }}</p>
                </div>
                <i class="fas fa-exclamation-triangle text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Active Items</p>
                    <p class="text-3xl font-bold mt-2">{{ $foodItems->where('is_active', true)->count() }}</p>
                </div>
                <i class="fas fa-star text-4xl opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Items Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($foodItems as $item)
            <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                <!-- Item Image or Placeholder -->
                <div class="relative h-48 bg-gradient-to-br from-orange-400 to-pink-500 overflow-hidden">
                    @if($item->image)
                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-utensils text-6xl text-white opacity-50"></i>
                        </div>
                    @endif

                    <!-- Status Badge -->
                    <div class="absolute top-3 right-3">
                        @if(!$item->is_active)
                            <span class="bg-gray-800 bg-opacity-80 text-white text-xs font-semibold px-3 py-1 rounded-full">Inactive</span>
                        @elseif($item->stock == 0)
                            <span class="bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">Out of Stock</span>
                        @elseif($item->stock < 10)
                            <span class="bg-yellow-500 text-white text-xs font-semibold px-3 py-1 rounded-full">Low Stock</span>
                        @else
                            <span class="bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-full">In Stock</span>
                        @endif
                    </div>

                    <!-- Category Badge -->
                    <div class="absolute top-3 left-3">
                <span class="bg-white bg-opacity-90 text-gray-800 text-xs font-medium px-3 py-1 rounded-full">
                    {{ $item->category->name }}
                </span>
                    </div>
                </div>

                <div class="p-5">
                    <!-- Item Name -->
                    <h3 class="text-lg font-bold text-gray-800 mb-1 line-clamp-1">{{ $item->name }}</h3>

                    <!-- Description -->
                    @if($item->description)
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $item->description }}</p>
                    @endif

                    <!-- Stock Info -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-box mr-2"></i>
                            <span>Stock: <span class="font-semibold {{ $item->stock < 10 ? 'text-red-600' : 'text-gray-800' }}">{{ $item->stock }}</span></span>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-3 mb-4">
                        <div class="flex items-baseline justify-center">
                            <span class="text-sm text-gray-600 mr-1">Rp</span>
                            <span class="text-2xl font-bold text-indigo-600">{{ number_format($item->price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <a href="{{ route('food-items.edit', $item) }}" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-center text-sm font-medium">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <form method="POST" action="{{ route('food-items.destroy', $item) }}" class="flex-1" onsubmit="return confirm('Delete this item?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm font-medium">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <div class="bg-orange-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-utensils text-4xl text-orange-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No Food Items Found</h3>
                    <p class="text-gray-600 mb-6">Add items to your menu to start selling</p>
                    <a href="{{ route('food-items.create') }}" class="inline-flex items-center bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i>Add Item
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($foodItems->hasPages())
        <div class="mt-8">
            {{ $foodItems->links() }}
        </div>
    @endif
@endsection
