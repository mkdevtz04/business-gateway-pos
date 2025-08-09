<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>{{ config('app.name', 'Business Gateway POS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-gray-100 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="bg-gray-800 text-white w-64 min-h-screen">
            <!-- Logo Section -->
            <div class="p-4 border-b border-gray-700">
                <div class="flex items-center justify-center">
                    <img src="{{ asset('storage/logos/mkdev.png') }}" alt="Logo" class="h-12 w-12">
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="mt-4">
                <a href="{{ route('dashboard') }}"
                   class="block px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('dashboard') ? 'bg-gray-700' : '' }}">
                    <div class="flex items-center">
                        <span class="ml-2">{{ __('Dashboard') }}</span>
                    </div>
                </a>

                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('categories.index') }}"
                       class="block px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('categories.*') ? 'bg-gray-700' : '' }}">
                        <div class="flex items-center">
                            <span class="ml-2">{{ __('Categories') }}</span>
                        </div>
                    </a>
                    <a href="{{ route('products.index') }}"
                       class="block px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('products.*') ? 'bg-gray-700' : '' }}">
                        <div class="flex items-center">
                            <span class="ml-2">{{ __('Products') }}</span>
                        </div>
                    </a>
                    <a href="{{ route('users.index') }}" 
                       class="block px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('users.*') ? 'bg-gray-700' : '' }}">
                        <div class="flex items-center">
                            <span class="ml-2">{{ __('Users') }}</span>
                        </div>
                    </a>
                @elseif(auth()->user()->role === 'clerk')
                    <a href="{{ route('pos.index') }}"
                       class="block px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('pos.*') ? 'bg-gray-700' : '' }}">
                        <div class="flex items-center">
                            <span class="ml-2">{{ __('Point of Sale') }}</span>
                        </div>
                    </a>
                    <a href="{{ route('orders.index') }}"
                       class="block px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('orders.*') ? 'bg-gray-700' : '' }}">
                        <div class="flex items-center">
                            <span class="ml-2">{{ __('Sales') }}</span>
                        </div>
                    </a>
                    {{-- <a href="{{ route('sales.index') }}"
                       class="block px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('sales.*') ? 'bg-gray-700' : '' }}">
                        <div class="flex items-center">
                            <span class="ml-2">{{ __('Sales History') }}</span>
                        </div>
                    </a> --}}
                @endif
                
            </nav>

            <!-- User Menu -->
            <div class="absolute bottom-0 w-64 border-t border-gray-700">
                <div class="p-4">
                    <div class="text-sm text-gray-300 mb-2">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-gray-500 mb-4">{{ auth()->user()->role }}</div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded-md">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Mobile Navigation Toggle -->
            <div class="md:hidden p-4">
                <button class="text-gray-600 hover:text-gray-900">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Add this before closing body tag -->
    @stack('scripts')
</body>
</html>
