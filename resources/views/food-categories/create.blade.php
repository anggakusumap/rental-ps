@extends('layouts.app')

@section('title', 'Create Food Category')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('food-categories.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Categories
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mt-4">Create New Category</h1>
            <p class="text-gray-600 mt-1">Organize your menu items into categories</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <form method="POST" action="{{ route('food-categories.store') }}">
                @csrf

                <!-- Category Name -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Category Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        placeholder="e.g., Beverages, Snacks, Meals"
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
                        placeholder="Describe what items belong in this category..."
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">{{ old('description') }}</textarea>
                    <p class="text-gray-500 text-sm mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Help customers understand what's in this category
                    </p>
                </div>

                <!-- Active Status -->
                <div class="mb-8">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 border-2 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-3 text-gray-700 font-medium">Active (visible in menu)</span>
                    </label>
                    <p class="text-gray-500 text-sm mt-1 ml-8">
                        Inactive categories won't appear when creating orders
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>Create Category
                    </button>
                    <a href="{{ route('food-categories.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold text-center">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Example Categories -->
        <div class="mt-8 bg-blue-50 border-2 border-blue-200 rounded-xl p-6">
            <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
                <i class="fas fa-lightbulb text-blue-600 mr-2"></i>
                Suggested Categories
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-white rounded-lg p-3 text-center">
                    <i class="fas fa-coffee text-2xl text-orange-500 mb-1"></i>
                    <p class="text-sm font-medium">Beverages</p>
                </div>
                <div class="bg-white rounded-lg p-3 text-center">
                    <i class="fas fa-cookie-bite text-2xl text-yellow-600 mb-1"></i>
                    <p class="text-sm font-medium">Snacks</p>
                </div>
                <div class="bg-white rounded-lg p-3 text-center">
                    <i class="fas fa-hamburger text-2xl text-red-500 mb-1"></i>
                    <p class="text-sm font-medium">Meals</p>
                </div>
                <div class="bg-white rounded-lg p-3 text-center">
                    <i class="fas fa-ice-cream text-2xl text-pink-500 mb-1"></i>
                    <p class="text-sm font-medium">Desserts</p>
                </div>
            </div>
        </div>
    </div>
@endsection
