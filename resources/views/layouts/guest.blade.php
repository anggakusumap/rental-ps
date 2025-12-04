<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ config('app.asset_version', '1') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ config('app.asset_version', '1') }}">

    <!-- Stylesheets -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-indigo-600 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-md">
    <!-- Logo/Brand -->
    <div class="text-center mb-8">
        <a href="/">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full shadow-lg mb-4 hover:scale-110 transition-transform duration-200">
                <i class="fas fa-gamepad text-4xl text-indigo-600"></i>
            </div>
        </a>
        <h1 class="text-4xl font-bold text-white mb-2">{{ config('app.name', 'The Room Playstation') }}</h1>
        <p class="text-indigo-100">PlayStation Rental Management</p>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-2xl shadow-2xl p-8">
        {{ $slot }}
    </div>

    <!-- Footer -->
    <div class="text-center mt-6 text-white text-sm">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'The Room Playstation') }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
