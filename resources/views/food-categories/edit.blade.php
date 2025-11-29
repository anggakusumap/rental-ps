@extends('layouts.app')

@section('title', 'Edit Food Category')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('food-categories.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Categories
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mt-4">Edit Category: {{ $foodCategory->name }}</h1>
            <p class="text-gray-600 mt-1">Update category information</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <form method="POST" action="{{ route('food-categories.update', $foodCategory) }}">
                @csrf
                @method('PUT')

                <!-- Category Name -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Category Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $foodCategory->name) }}"
                        required
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('name') border-red-500 @enderror">
                    @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Description (Optional)
                    </label>
                    <textarea
                        name="description"
                        rows="4"
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">{{ old('description', $foodCategory->description) }}</textarea>
                </div>

                <!-- Active Status -->
                <div class="mb-8">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $foodCategory->is_active) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 border-2 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-3 text-gray-700 font-medium">Active (visible in menu)</span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>Update Category
                    </button>
                    <a href="{{ route('food-categories.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold text-center">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Items Count Info -->
        @if($foodCategory->foodItems()->count() > 0)
            <div class="mt-6 bg-blue-50 border-2 border-blue-200 rounded-xl p-6">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-600 text-xl mr-3"></i>
                    <div>
                        <p class="font-semibold text-blue-900">{{ $foodCategory->foodItems()->count() }} item(s) in this category</p>
                        <p class="text-sm text-blue-700 mt-1">These items will remain associated with this category after editing.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
