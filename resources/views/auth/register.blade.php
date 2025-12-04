<x-guest-layout>
    <x-slot name="title">Register - {{ config('app.name') }}</x-slot>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Get Started</h2>
        <p class="text-gray-600 text-sm mt-1">Fill in your details to create an account</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-4">
            <x-input-label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-user mr-2 text-gray-400"></i>{{ __('Full Name') }}
            </x-input-label>
            <x-text-input
                id="name"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition"
                placeholder="John Doe"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mb-4">
            <x-input-label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-envelope mr-2 text-gray-400"></i>{{ __('Email Address') }}
            </x-input-label>
            <x-text-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username"
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition"
                placeholder="your@email.com"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <x-input-label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-lock mr-2 text-gray-400"></i>{{ __('Password') }}
            </x-input-label>
            <x-text-input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition"
                placeholder="Create a strong password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
            <x-input-label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-lock mr-2 text-gray-400"></i>{{ __('Confirm Password') }}
            </x-input-label>
            <x-text-input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition"
                placeholder="Confirm your password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Register Button -->
        <x-primary-button class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-3 rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 transition-all duration-200 shadow-lg justify-center">
            <i class="fas fa-user-plus mr-2"></i>{{ __('Create Account') }}
        </x-primary-button>
    </form>

    <!-- Login Link -->
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
            {{ __('Already have an account?') }}
            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                {{ __('Sign in here') }}
            </a>
        </p>
    </div>
</x-guest-layout>
