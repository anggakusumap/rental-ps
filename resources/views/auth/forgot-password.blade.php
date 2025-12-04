<x-guest-layout>
    <x-slot name="title">Forgot Password - {{ config('app.name') }}</x-slot>

    <div class="mb-6">
        <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-key text-3xl text-indigo-600"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 text-center">{{ __('Forgot Password?') }}</h2>
        <p class="text-gray-600 text-sm mt-2 text-center">
            {{ __('No problem! Enter your email address and we\'ll send you a link to reset your password.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center" :status="session('status')">
        <x-slot:prepend><i class="fas fa-check-circle mr-2"></i></x-slot:prepend>
    </x-auth-session-status>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-6">
            <x-input-label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-envelope mr-2 text-gray-400"></i>{{ __('Email Address') }}
            </x-input-label>
            <x-text-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition"
                placeholder="your@email.com"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <x-primary-button class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-3 rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 transition-all duration-200 shadow-lg justify-center">
            <i class="fas fa-paper-plane mr-2"></i>{{ __('Email Password Reset Link') }}
        </x-primary-button>
    </form>

    <!-- Back to Login -->
    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-indigo-600 font-medium transition">
            <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to login') }}
        </a>
    </div>
</x-guest-layout>
