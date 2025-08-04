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
        <div class="max-w-7xl mx-auto px-4 py-2 flex justify-between items-center">
            <div>
                <img src="{{ asset('storage/logos/mkdev.png') }}" alt="Logo" class="h-12">
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-gray-800">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')"
                        onclick="event.preventDefault();
                                                this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </div>

        </div>
    </header>
    <main class="max-w-7xl mx-auto py-6 px-4">
        @yield('content')
    </main>
</body>

</html>
