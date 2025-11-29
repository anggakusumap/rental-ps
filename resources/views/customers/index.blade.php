@extends('layouts.app')

@section('title', 'Customers')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Customer Management</h1>
                <p class="text-gray-600 mt-1">Manage your customer database</p>
            </div>
            <a href="{{ route('customers.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition shadow-lg hover:shadow-xl flex items-center">
                <i class="fas fa-plus mr-2"></i>Add Customer
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Customers</p>
                    <p class="text-3xl font-bold mt-2">{{ $customers->total() }}</p>
                </div>
                <i class="fas fa-users text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Active</p>
                    <p class="text-3xl font-bold mt-2">{{ $customers->where('is_active', true)->count() }}</p>
                </div>
                <i class="fas fa-check-circle text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Inactive</p>
                    <p class="text-3xl font-bold mt-2">{{ $customers->where('is_active', false)->count() }}</p>
                </div>
                <i class="fas fa-user-slash text-4xl opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Contact</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Sessions</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Orders</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="bg-indigo-100 rounded-full w-12 h-12 flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-indigo-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $customer->name }}</p>
                                    @if($customer->email)
                                        <p class="text-xs text-gray-500">{{ $customer->email }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($customer->phone)
                                <div class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-phone mr-2 text-green-600"></i>
                                    {{ $customer->phone }}
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">No phone</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                <i class="fas fa-gamepad mr-1"></i>
                                {{ $customer->rental_sessions_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                <i class="fas fa-shopping-cart mr-1"></i>
                                {{ $customer->orders_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($customer->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    <i class="fas fa-ban mr-1"></i>Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-3 items-center">
                                <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-900 font-medium text-sm">
                                    View
                                </a>
                                <a href="{{ route('customers.edit', $customer) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                    Edit
                                </a>
                                @if($customer->rental_sessions_count == 0 && $customer->orders_count == 0)
                                    <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="inline" onsubmit="return confirm('Delete this customer?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-sm mb-1">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mb-4">
                                    <i class="fas fa-users text-3xl text-indigo-600"></i>
                                </div>
                                <p class="text-gray-500 text-lg font-medium">No customers found</p>
                                <p class="text-gray-400 text-sm mt-1">Add your first customer</p>
                                <a href="{{ route('customers.create') }}" class="mt-4 bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                                    Add Customer
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
@endsection
