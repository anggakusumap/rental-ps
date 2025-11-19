<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PS Rental Management')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
<nav class="bg-indigo-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <i class="fas fa-gamepad text-2xl mr-3"></i>
                <span class="text-xl font-bold">PS Rental</span>
            </div>
            <div class="flex items-center space-x-4">
                <span>{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="bg-indigo-700 px-4 py-2 rounded hover:bg-indigo-800">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>

<div class="flex">
    <aside class="w-64 bg-white shadow-md min-h-screen">
        <nav class="mt-5">
            <a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                <i class="fas fa-dashboard w-5"></i>
                <span class="ml-3">Dashboard</span>
            </a>
            <a href="{{ route('rental-sessions.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-100 {{ request()->routeIs('rental-sessions.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                <i class="fas fa-play w-5"></i>
                <span class="ml-3">Rental Sessions</span>
            </a>
            <a href="{{ route('consoles.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-100 {{ request()->routeIs('consoles.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                <i class="fas fa-gamepad w-5"></i>
                <span class="ml-3">Consoles</span>
            </a>
            <a href="{{ route('console-types.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-100 {{ request()->routeIs('console-types.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                <i class="fas fa-list w-5"></i>
                <span class="ml-3">Console Types</span>
            </a>
            <a href="{{ route('packages.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-100 {{ request()->routeIs('packages.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                <i class="fas fa-box w-5"></i>
                <span class="ml-3">Packages</span>
            </a>
            <a href="{{ route('orders.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-100 {{ request()->routeIs('orders.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                <i class="fas fa-shopping-cart w-5"></i>
                <span class="ml-3">Food Orders</span>
            </a>
            <a href="{{ route('food-items.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-100 {{ request()->routeIs('food-items.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                <i class="fas fa-utensils w-5"></i>
                <span class="ml-3">Food Items</span>
            </a>
            <a href="{{ route('invoices.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-100 {{ request()->routeIs('invoices.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                <i class="fas fa-file-invoice w-5"></i>
                <span class="ml-3">Invoices</span>
            </a>
            <a href="{{ route('reports.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-100 {{ request()->routeIs('reports.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                <i class="fas fa-chart-bar w-5"></i>
                <span class="ml-3">Reports</span>
            </a>
        </nav>
    </aside>

    <main class="flex-1 p-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>
