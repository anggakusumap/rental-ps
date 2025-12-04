<x-guest-layout>
    <x-slot name="title">Confirm Password - {{ config('app.name') }}</x-slot>

    <div class="mb-6">
        <div class="bg-yellow-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-shield-alt text-3xl text-yellow-600"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 text-center">{{ __('Confirm Password') }}</h2>
        <p class="text-gray-600 text-sm mt-2 text-center">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div class="mb-6">
            <x-input-label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-lock mr-2 text-gray-400"></i>{{ __('Password') }}
            </x-input-label>
            <x-text-input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition"
                placeholder="Enter your password"
                autofocus
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <x-primary-button class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-3 rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 transition-all duration-200 shadow-lg justify-center">
            <i class="fas fa-check-circle mr-2"></i>{{ __('Confirm') }}
        </x-primary-button>
    </form>
</x-guest-layout>
