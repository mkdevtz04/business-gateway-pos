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
        <aside class="bg-[#2545d4] text-white w-64 min-h-screen print:hidden">
            <div class="p-4 border-b border-[#f1f1f1]">
                <div class="flex items-center justify-center">
                    <img src="{{ asset('storage/logos/mkdev.png') }}" alt="Logo" class="h-12 w-12">
                </div>
            </div>

            <nav class="mt-4">
                <a href="/dashboard"
                    class="block px-4 py-3 text-[#B0D6B0]  hover:text-black {{ request()->routeIs('dashboard') ? ' ' : '' }}">
                    <div class="flex items-center">
                        <span class="ml-2">{{ __('Dashboard') }}</span>
                    </div>
                </a>

                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('categories.index') }}"
                        class="block px-4 py-3 text-[#B0D6B0]  hover:text-black {{ request()->routeIs('categories.*') ?  : '' }}">
                        <div class="flex items-center">
                            <span class="ml-2">{{ __('Categories') }}</span>
                        </div>
                    </a>
                    <a href="{{ route('products.index') }}"
                        class="block px-4 py-3 text-[#B0D6B0]  hover:text-black {{ request()->routeIs('products.*') ?  : '' }}">
                        <div class="flex items-center">
                            <span class="ml-2">{{ __('Products') }}</span>
                        </div>
                    </a>

                    <a href="/admin/orders"
                        class="block px-4 py-3 text-[#B0D6B0]  hover:text-black {{ request()->routeIs('orders.*') ?  : '' }}">
                        <div class="flex items-center">
                            <span class="ml-2">{{ __('Sales') }}</span>
                        </div>
                    </a>

                    <a href="{{ route('users.index') }}"
                        class="block px-4 py-3 text-[#B0D6B0]  hover:text-black {{ request()->routeIs('users.*') ?  : '' }}">
                        <div class="flex items-center">
                            <span class="ml-2">{{ __('Users') }}</span>
                        </div>
                    </a>
                @elseif(auth()->user()->role === 'clerk')
                    <a href="{{ route('pos.index') }}"
                        class="block px-4 py-3 text-[#B0D6B0]  hover:text-black {{ request()->routeIs('pos.*') ?  : '' }}">
                        <div class="flex items-center">
                            <span class="ml-2">{{ __('Point of Sale') }}</span>
                        </div>
                    </a>
                    <a href="{{ route('orders.index') }}"
                        class="block px-4 py-3 text-[#B0D6B0]  hover:text-black {{ request()->routeIs('orders.*') ?  : '' }}">
                        <div class="flex items-center">
                            <span class="ml-2">{{ __('Sales') }}</span>
                        </div>
                    </a>
                    {{-- <a href="{{ route('sales.index') }}"
                       class="block px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-black {{ request()->routeIs('sales.*') ? 'bg-gray-700' : '' }}">
                        <div class="flex items-center">
                            <span class="ml-2">{{ __('Sales History') }}</span>
                        </div>
                    </a> --}}
                @endif
            </nav>

            <div class="absolute bottom-0 w-64 border-t border-[#1E771E]">
                <div class="p-4">
                    <div class="text-sm text-[#B0D6B0] mb-2">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-[#FFD700] mb-4">{{ auth()->user()->role }}</div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-[#B0D6B0]  hover:text-black rounded-md">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="md:hidden p-4">
                <button class="text-[#8B4513] hover:text-[#5C2B0B]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-[#F5F5DC]">
                <div class="container mx-auto px-6 py-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>