<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>{{ config('app.name', 'Business Gateway POS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-gradient {
            background: linear-gradient(180deg, #2545d4 0%, #1e3a8a 100%);
        }

        .nav-item {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .nav-item:hover::before {
            left: 100%;
        }

        .nav-item.active {
            background: rgba(255, 255, 255, 0.1);
            border-right: 4px solid #60a5fa;
        }

        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.05);
        }

        .main-content {
            background: linear-gradient(135deg, #f5f5dc 0%, #e5e5d5 100%);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #2545d4 #f1f1f1;
        }

        .main-content::-webkit-scrollbar {
            width: 6px;
        }

        .main-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .main-content::-webkit-scrollbar-thumb {
            background: #2545d4;
            border-radius: 3px;
        }

        .main-content::-webkit-scrollbar-thumb:hover {
            background: #1e3a8a;
        }

        .nav-icon-container {
            @apply w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center mr-3 group-hover:bg-white/20 transition-colors;
        }

        .nav-icon {
            @apply text-sm;
        }

        .logo-container {
            @apply w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center glass-effect;
        }

        .profile-icon {
            @apply w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-base;
        }

        .sidebar-nav {
            height: calc(100vh - 200px);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #60a5fa #2545d4;
            margin-right: -8px;
            padding-right: 8px;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: #2545d4;
            border-radius: 2px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: #60a5fa;
            border-radius: 2px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: #93c5fd;
        }

        #welcome-message {
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <aside class="sidebar-gradient text-white w-64 min-h-screen print:hidden shadow-lg relative">
            <div class="p-4 border-b border-white/20">
                <div class="flex items-center justify-center">
                    <div class="relative">
                        <div class="logo-container">
                            <img src="{{ asset('storage/logos/mkdev.png') }}" alt="Logo" class="h-8 w-8 object-contain">
                        </div>
                        <div class="absolute -inset-1 bg-gradient-to-r from-blue-400 to-blue-600 rounded-xl blur opacity-30"></div>
                    </div>
                </div>
                <div class="text-center mt-2">
                    <h1 class="text-lg font-bold text-white">Business Gateway</h1>
                    <p class="text-blue-200 text-xs">Point of Sale System</p>
                </div>
            </div>

            <nav class="sidebar-nav mt-4 px-4">
                <a href="/dashboard"
                    class="nav-item block px-4 py-3 text-white hover:bg-white/10 rounded-lg mb-2 group {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <div class="flex items-center">
                        <div class="nav-icon-container">
                            <i class="fas fa-tachometer-alt nav-icon"></i>
                        </div>
                        <span class="font-medium">{{ __('Dashboard') }}</span>
                    </div>
                </a>

                @if (auth()->user()->role === 'admin')
                    <div class="mb-4">
                        <h3 class="text-blue-200 text-xs uppercase tracking-wider font-semibold mb-2 px-4">Administration</h3>
                        <a href="{{ route('categories.index') }}"
                            class="nav-item block px-4 py-3 text-white hover:bg-white/10 rounded-lg mb-2 group {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="nav-icon-container">
                                    <i class="fas fa-tags nav-icon"></i>
                                </div>
                                <span class="font-medium">{{ __('Categories') }}</span>
                            </div>
                        </a>
                        <a href="{{ route('products.index') }}"
                            class="nav-item block px-4 py-3 text-white hover:bg-white/10 rounded-lg mb-2 group {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="nav-icon-container">
                                    <i class="fas fa-box nav-icon"></i>
                                </div>
                                <span class="font-medium">{{ __('Products') }}</span>
                            </div>
                        </a>
                        <a href="/admin/orders"
                            class="nav-item block px-4 py-3 text-white hover:bg-white/10 rounded-lg mb-2 group {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="nav-icon-container">
                                    <i class="fas fa-chart-line nav-icon"></i>
                                </div>
                                <span class="font-medium">{{ __('Sales') }}</span>
                            </div>
                        </a>
                        <a href="{{ route('users.index') }}"
                            class="nav-item block px-4 py-3 text-white hover:bg-white/10 rounded-lg mb-2 group {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="nav-icon-container">
                                    <i class="fas fa-users nav-icon"></i>
                                </div>
                                <span class="font-medium">{{ __('Users') }}</span>
                            </div>
                        </a>
                    </div>
                @elseif(auth()->user()->role === 'clerk')
                    <div class="mb-4">
                        <h3 class="text-blue-200 text-xs uppercase tracking-wider font-semibold mb-2 px-4">Sales Operations</h3>
                        <a href="{{ route('pos.index') }}"
                            class="nav-item block px-4 py-3 text-white hover:bg-white/10 rounded-lg mb-2 group {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="nav-icon-container">
                                    <i class="fas fa-cash-register nav-icon"></i>
                                </div>
                                <span class="font-medium">{{ __('Point of Sale') }}</span>
                            </div>
                        </a>
                        <a href="{{ route('orders.index') }}"
                            class="nav-item block px-4 py-3 text-white hover:bg-white/10 rounded-lg mb-2 group {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="nav-icon-container">
                                    <i class="fas fa-receipt nav-icon"></i>
                                </div>
                                <span class="font-medium">{{ __('Sales') }}</span>
                            </div>
                        </a>
                    </div>
                @endif
            </nav>

            <div class="absolute bottom-0 w-64 border-t border-white/20">
                <div class="p-4">
                    <div class="bg-white/10 rounded-lg p-3 glass-effect">
                        <div class="flex items-center mb-3">
                            <div class="profile-icon">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="ml-3">
                                <div class="font-semibold text-white text-sm">{{ auth()->user()->name }}</div>
                                <div class="text-blue-200 text-xs capitalize">{{ auth()->user()->role }}</div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-center px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-200 hover:text-white rounded-md transition-all duration-200 group">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="md:hidden p-4 bg-white border-b">
                <div class="flex items-center justify-between">
                    <button class="p-2 rounded-md bg-[#2545d4] text-white hover:bg-[#1e3a8a]">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-800">Business Gateway POS</h1>
                    <div class="w-8"></div>
                </div>
            </div>

            <main class="flex-1 main-content">
                <div class="container mx-auto px-6 py-8">
                    <div class="mb-6">
                        <div id="welcome-message" class="bg-white rounded-lg shadow-md p-6 opacity-100 transition-opacity duration-500">
                            <h1 class="text-xl font-bold text-gray-800 mb-2">Welcome back, {{ auth()->user()->name }}!</h1>
                            <p class="text-gray-600 text-sm">Here's what's happening with your business today.</p>
                        </div>
                    </div>
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');

                this.appendChild(ripple);

                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Welcome message fade out
        document.addEventListener('DOMContentLoaded', function() {
            const welcomeMessage = document.getElementById('welcome-message');
            
            if (welcomeMessage) {
                setTimeout(() => {
                    welcomeMessage.style.opacity = '0';
                    setTimeout(() => {
                        welcomeMessage.style.display = 'none';
                    }, 500); // Wait for fade out animation to complete
                }, 5000); // 5 seconds delay
            }
        });
    </script>

    <style>
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    </style>

    @stack('scripts')
</body>

</html>