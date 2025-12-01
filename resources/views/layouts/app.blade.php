<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'The Room PlayStation Management')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        /* Make Select2 match Tailwind input styling */
        .select2-container .select2-selection--single {
            height: 42px !important;
            border: 2px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            padding: 6px 12px !important;
        }

        .select2-container .select2-selection__arrow {
            height: 42px !important;
            right: 10px !important;
        }

        .select2-container--default .select2-results__option--highlighted {
            background-color: #4f46e5 !important;
            color: white !important;
        }

        /* Hide scrollbar for horizontal scroll but keep functionality */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-gray-100">
@auth
    <!-- Mobile Navigation -->
    <nav class="bg-indigo-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14 sm:h-16">
                <!-- Logo/Brand -->
                <div class="flex items-center">
                    <i class="fas fa-gamepad text-xl sm:text-2xl mr-2"></i>
                    <span class="text-base sm:text-xl font-bold truncate">The Room PS</span>
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <span class="text-xs sm:text-base hidden sm:inline">{{ auth()->user()->name }}</span>
                    <button type="button" class="lg:hidden inline-flex items-center justify-center p-2 rounded-md hover:bg-indigo-700 focus:outline-none" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                        @csrf
                        <button type="submit" class="bg-indigo-700 px-3 sm:px-4 py-1.5 sm:py-2 rounded text-sm hover:bg-indigo-800">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex flex-col lg:flex-row">
        <!-- Mobile Menu Overlay -->
        <div id="mobileMenuOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" onclick="toggleMobileMenu()"></div>

        <!-- Sidebar (Desktop & Mobile Slide-out) -->
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out w-64 bg-white shadow-md z-50 lg:z-0 overflow-y-auto">
            <!-- Mobile close button -->
            <div class="lg:hidden flex justify-between items-center p-4 border-b">
                <span class="font-semibold text-gray-800">Menu</span>
                <button onclick="toggleMobileMenu()" class="p-2 rounded-md hover:bg-gray-100">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
            </div>

            <nav class="mt-3 lg:mt-5 pb-20 lg:pb-0">
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 sm:px-6 py-3 text-sm hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
                <a href="{{ route('customers.index') }}" class="flex items-center px-4 sm:px-6 py-3 text-sm hover:bg-gray-100 {{ request()->routeIs('customers.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                    <i class="fas fa-users w-5"></i>
                    <span class="ml-3">Customers</span>
                </a>
                <a href="{{ route('rental-sessions.index') }}" class="flex items-center px-4 sm:px-6 py-3 text-sm hover:bg-gray-100 {{ request()->routeIs('rental-sessions.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                    <i class="fas fa-play w-5"></i>
                    <span class="ml-3">Rental Sessions</span>
                </a>
                <a href="{{ route('consoles.index') }}" class="flex items-center px-4 sm:px-6 py-3 text-sm hover:bg-gray-100 {{ request()->routeIs('consoles.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                    <i class="fas fa-gamepad w-5"></i>
                    <span class="ml-3">Consoles</span>
                </a>
                <a href="{{ route('console-types.index') }}" class="flex items-center px-4 sm:px-6 py-3 text-sm hover:bg-gray-100 {{ request()->routeIs('console-types.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                    <i class="fas fa-list w-5"></i>
                    <span class="ml-3">Console Types</span>
                </a>
                <a href="{{ route('packages.index') }}" class="flex items-center px-4 sm:px-6 py-3 text-sm hover:bg-gray-100 {{ request()->routeIs('packages.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                    <i class="fas fa-box w-5"></i>
                    <span class="ml-3">Packages</span>
                </a>
                <a href="{{ route('orders.index') }}" class="flex items-center px-4 sm:px-6 py-3 text-sm hover:bg-gray-100 {{ request()->routeIs('orders.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span class="ml-3">Food Orders</span>
                </a>
                <a href="{{ route('food-items.index') }}" class="flex items-center px-4 sm:px-6 py-3 text-sm hover:bg-gray-100 {{ request()->routeIs('food-items.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                    <i class="fas fa-utensils w-5"></i>
                    <span class="ml-3">Food Items</span>
                </a>
                <a href="{{ route('invoices.index') }}" class="flex items-center px-4 sm:px-6 py-3 text-sm hover:bg-gray-100 {{ request()->routeIs('invoices.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                    <i class="fas fa-file-invoice w-5"></i>
                    <span class="ml-3">Invoices</span>
                </a>
                <a href="{{ route('reports.index') }}" class="flex items-center px-4 sm:px-6 py-3 text-sm hover:bg-gray-100 {{ request()->routeIs('reports.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                    <i class="fas fa-chart-bar w-5"></i>
                    <span class="ml-3">Reports</span>
                </a>

                <!-- Mobile Logout -->
                <div class="lg:hidden px-4 sm:px-6 py-3 border-t mt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-3 sm:p-6 lg:p-8 min-h-screen">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-3 sm:px-4 py-2 sm:py-3 rounded mb-3 sm:mb-4 text-sm sm:text-base">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-3 sm:px-4 py-2 sm:py-3 rounded mb-3 sm:mb-4 text-sm sm:text-base">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileMenuOverlay');

            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
@else
    <div class="flex items-center justify-center min-h-screen p-4">
        @yield('content')
    </div>
@endauth
</body>
</html>
