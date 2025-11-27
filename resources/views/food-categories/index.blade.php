@extends('layouts.app')

@section('title', 'Food Categories')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Food Categories</h1>
                <p class="text-gray-600 mt-1">Organize your menu items</p>
            </div>
            <a href="{{ route('food-categories.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition shadow-lg hover:shadow-xl flex items-center">
                <i class="fas fa-plus mr-2"></i>Add Category
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($categories as $category)
            <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                <div class="h-2 bg-gradient-to-r from-orange-400 to-pink-500"></div>

                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center">
                            <div class="bg-gradient-to-br from-orange-500 to-pink-600 rounded-xl p-3 mr-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-folder text-2xl text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">{{ $category->name }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $category->food_items_count }} items</p>
                            </div>
                        </div>

                        @if($category->is_active)
                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">Active</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-3 py-1 rounded-full">Inactive</span>
                        @endif
                    </div>

                    @if($category->description)
                        <p class="text-gray-600 text-sm mb-4">{{ $category->description }}</p>
                    @endif

                    <div class="flex space-x-2 mt-4">
                        <a href="{{ route('food-categories.edit', $category) }}" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-center font-medium text-sm">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        @if($category->food_items_count == 0)
                            <form method="POST" action="{{ route('food-categories.destroy', $category) }}" class="flex-1" onsubmit="return confirm('Delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition font-medium text-sm">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                        @else
                            <button disabled class="flex-1 bg-gray-300 text-gray-500 px-4 py-2 rounded-lg cursor-not-allowed font-medium text-sm" title="Cannot delete - has items">
                                <i class="fas fa-lock mr-1"></i>Delete
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <div class="bg-orange-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-folder-open text-4xl text-orange-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No Categories Found</h3>
                    <p class="text-gray-600 mb-6">Create your first category to organize menu items</p>
                    <a href="{{ route('food-categories.create') }}" class="inline-flex items-center bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i>Add Category
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($categories->hasPages())
        <div class="mt-8">
            {{ $categories->links() }}
        </div>
    @endif
@endsection
