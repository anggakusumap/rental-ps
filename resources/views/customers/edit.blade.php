@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('customers.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Customers
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mt-4">Edit Customer: {{ $customer->name }}</h1>
            <p class="text-gray-600 mt-1">Update customer information</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <form method="POST" action="{{ route('customers.update', $customer) }}">
                @csrf
                @method('PUT')

                <!-- Customer Name -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Customer Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $customer->name) }}"
                        required
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
                            value="{{ old('phone', $customer->phone) }}"
                            placeholder="e.g., 081234567890"
                            class="w-full border-2 border-gray-300 rounded-lg pl-12 pr-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('phone') border-red-500 @enderror">
                    </div>
                    @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
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
                            value="{{ old('email', $customer->email) }}"
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
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('address') border-red-500 @enderror">{{ old('address', $customer->address) }}</textarea>
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
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">{{ old('notes', $customer->notes) }}</textarea>
                    <p class="text-gray-500 text-sm mt-1">
                        Preferences, special requests, or important information
                    </p>
                </div>

                <!-- Active Status -->
                <div class="mb-8">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $customer->is_active) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 border-2 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-3 text-gray-700 font-medium">Active Customer</span>
                    </label>
                    <p class="text-gray-500 text-sm mt-1 ml-8">
                        Inactive customers won't appear in customer selection lists
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>Update Customer
                    </button>
                    <a href="{{ route('customers.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold text-center">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Customer Activity Summary -->
        <div class="mt-8 bg-blue-50 border-2 border-blue-200 rounded-xl p-6">
            <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
                <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                Customer Activity
            </h3>
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-600 mb-1">Gaming Sessions</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $customer->rentalSessions->count() }}</p>
                </div>
                <div class="bg-white rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-600 mb-1">Food Orders</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $customer->orders->count() }}</p>
                </div>
                <div class="bg-white rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-600 mb-1">Total Spent</p>
                    <p class="text-lg font-bold text-green-600">
                        Rp {{ number_format($customer->rentalSessions->where('status', 'completed')->sum('total_cost'), 0, ',', '.') }}
                    </p>
                </div>
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                    View Full Customer Profile →
                </a>
            </div>
        </div>

        <!-- Warning if customer has activity -->
        @if($customer->rentalSessions->count() > 0 || $customer->orders->count() > 0)
            <div class="mt-6 bg-yellow-50 border-2 border-yellow-200 rounded-xl p-6">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3 mt-1"></i>
                    <div>
                        <h3 class="font-semibold text-yellow-900 mb-1">Customer Has Activity</h3>
                        <p class="text-sm text-yellow-800">
                            This customer has {{ $customer->rentalSessions->count() }} gaming session(s) and {{ $customer->orders->count() }} order(s).
                            Changes to contact information won't affect past records.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
