@extends('layouts.app')

@section('title', 'Add Food Item')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('food-items.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Menu
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mt-4">Add New Food Item</h1>
            <p class="text-gray-600 mt-1">Add a new item to your menu</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <form method="POST" action="{{ route('food-items.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Item Image -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Item Photo (Optional)
                    </label>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <img id="image-preview" src="" alt="Preview" class="hidden w-32 h-32 object-cover rounded-lg border-2 border-gray-300">
                            <div id="image-placeholder" class="w-32 h-32 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                                <i class="fas fa-image text-4xl text-gray-400"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <input
                                type="file"
                                name="image"
                                id="image"
                                accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="text-gray-500 text-xs mt-1">PNG, JPG, GIF up to 2MB</p>
                            @error('image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Category -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="food_category_id" required class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('food_category_id') border-red-500 @enderror">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('food_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('food_category_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Item Name -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Item Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        placeholder="e.g., Coca Cola, Burger, French Fries"
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
                        rows="3"
                        placeholder="Describe the item, ingredients, or special features..."
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">{{ old('description') }}</textarea>
                </div>

                <!-- Price & Stock Grid -->
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <!-- Price -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            Price (Rp) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-medium">Rp</span>
                            </div>
                            <input
                                type="number"
                                name="price"
                                value="{{ old('price') }}"
                                required
                                min="0"
                                step="500"
                                placeholder="5000"
                                class="w-full border-2 border-gray-300 rounded-lg pl-14 pr-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('price') border-red-500 @enderror">
                        </div>
                        @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stock -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            Initial Stock <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="stock"
                            value="{{ old('stock', 0) }}"
                            required
                            min="0"
                            placeholder="100"
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('stock') border-red-500 @enderror">
                        @error('stock')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Active Status -->
                <div class="mb-8">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 border-2 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-3 text-gray-700 font-medium">Active (available for sale)</span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>Add Item
                    </button>
                    <a href="{{ route('food-items.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold text-center">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('image-preview').src = e.target.result;
                    document.getElementById('image-preview').classList.remove('hidden');
                    document.getElementById('image-placeholder').classList.add('hidden');
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
