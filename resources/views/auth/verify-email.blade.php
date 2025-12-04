<x-guest-layout>
    <x-slot name="title">Verify Email - {{ config('app.name') }}</x-slot>

    <div class="mb-6">
        <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-envelope-open-text text-3xl text-blue-600"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 text-center">{{ __('Verify Your Email') }}</h2>
        <p class="text-gray-600 text-sm mt-2 text-center">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="space-y-3">
        <!-- Resend Verification Email -->
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-3 rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 transition-all duration-200 shadow-lg justify-center">
                <i class="fas fa-paper-plane mr-2"></i>{{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-secondary-button type="submit" class="w-full bg-gray-100 text-gray-700 font-semibold py-3 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-all duration-200 justify-center">
                <i class="fas fa-sign-out-alt mr-2"></i>{{ __('Log Out') }}
            </x-secondary-button>
        </form>
    </div>
</x-guest-layout>
