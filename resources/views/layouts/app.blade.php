<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>{{ config('app.name', 'Business Gateway POS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans">
    <header class="bg-gray-200 border-b border-gray-300">
        <div class="max-w-7xl mx-auto px-4 py-2">
            <div class="flex justify-between items-center">
                <!-- Logo Section and Navigation Container -->
                <div class="flex items-center space-x-2">
                    <!-- Logo Section -->
                    <div class="flex items-center">
                        <img src="{{ asset('storage/logos/mkdev.png') }}" alt="Logo" class="h-10 w-10">
                    </div>

                    <!-- Navigation Links -->
                    <nav class="hidden md:flex space-x-1">
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md">
                            {{ __('Dashboard') }}
                        </a>

                        @if(auth()->user()->role === 'admin')
                        {{-- <a href="{{ route('categories.index') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md">
                            {{ __('Categories') }}
                        </a> --}}
                        <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md">
                            {{ __('Products') }}
                        </a>
                        @endif
                    </nav>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <span class="text-gray-800">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div class="md:hidden mt-2">
                <div class="space-y-0.5">
                    <a href="{{ route('dashboard') }}" class="block text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md">
                        {{ __('Dashboard') }}
                    </a>

                    @if(auth()->user()->role === 'admin')
                    {{-- <a href="{{ route('categories.index') }}" class="block text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md">
                        {{ __('Categories') }}
                    </a> --}}
                    <a href="{{ route('products.index') }}" class="block text-white-600 hover:text-gray-900 px-3 py-2 rounded-md">cat
                        {{ __('Products') }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 px-4">
        @yield('content')
    </main>
</body>

</html>
