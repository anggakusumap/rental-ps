@extends('layouts.app')

@section('title', 'Add Customer')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('customers.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Customers
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mt-4">Add New Customer</h1>
            <p class="text-gray-600 mt-1">Register a new customer to the system</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <form method="POST" action="{{ route('customers.store') }}">
                @csrf

                <!-- Customer Name -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Customer Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        placeholder="e.g., John Doe"
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('name') border-red-500 @enderror">
                    @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Phone Number
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="e.g., 081234567890"
                            class="w-full border-2 border-gray-300 rounded-lg pl-12 pr-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('phone') border-red-500 @enderror">
                    </div>
                    @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Optional but recommended for contact
                    </p>
                </div>

                <!-- Email -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="e.g., customer@example.com"
                            class="w-full border-2 border-gray-300 rounded-lg pl-12 pr-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('email') border-red-500 @enderror">
                    </div>
                    @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Address
                    </label>
                    <textarea
                        name="address"
                        rows="3"
                        placeholder="Enter customer address..."
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                    @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Notes (Optional)
                    </label>
                    <textarea
                        name="notes"
                        rows="3"
                        placeholder="Any additional notes about this customer..."
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">{{ old('notes') }}</textarea>
                    <p class="text-gray-500 text-sm mt-1">
                        Preferences, special requests, or important information
                    </p>
                </div>

                <!-- Active Status -->
                <div class="mb-8">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 border-2 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-3 text-gray-700 font-medium">Active Customer</span>
                    </label>
                    <p class="text-gray-500 text-sm mt-1 ml-8">
                        Inactive customers won't appear in customer selection lists
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>Add Customer
                    </button>
                    <a href="{{ route('customers.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold text-center">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Quick Tips -->
        <div class="mt-8 bg-blue-50 border-2 border-blue-200 rounded-xl p-6">
            <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
                <i class="fas fa-lightbulb text-blue-600 mr-2"></i>
                Customer Benefits
            </h3>
            <ul class="space-y-2 text-sm text-blue-800">
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                    <span>Track customer history and preferences</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                    <span>Quick selection during order creation</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                    <span>Build customer loyalty and relationships</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                    <span>Easy contact for promotions and updates</span>
                </li>
            </ul>
        </div>
    </div>
@endsection
