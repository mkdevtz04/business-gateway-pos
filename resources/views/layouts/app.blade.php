<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Business Gateway POS') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .sidebar {
            background: #2d3748;
            color: white;
            width: 250px;
            min-height: 100vh;
        }

        .main-content {
            background: #f7fafc;
            overflow-y: auto;
        }

        .main-content::-webkit-scrollbar {
            width: 10px;
        }

        .main-content::-webkit-scrollbar-track {
            background: #edf2f7;
        }

        .main-content::-webkit-scrollbar-thumb {
            background: #4a5568;
            /* border-radius: 3px; */
        }

        .nav-item {
            display: flex; /* Ensure flex is applied directly to nav-item */
            align-items: center; /* Vertically align items */
            padding: 8px 12px; /* Increased padding for better spacing */
            color: white;
            border-radius: 6px;
            margin-bottom: 4px; /* Added a small margin between items */
        }

        .nav-item.active {
            background: #4a5568;
        }

        .nav-icon-container {
            width: 24px; /* Increased width for better icon visibility */
            height: 24px; /* Increased height */
            background: #4a5568;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px; /* Increased margin for better separation from text */
            flex-shrink: 0; /* Prevent icon container from shrinking */
        }

        .nav-icon {
            font-size: 16px; /* Adjusted icon size to fit better in the container */
            color: white; /* Ensure icon color is white */
        }

        .logo-container {
            width: 40px; /* Increased logo container size */
            height: 40px; /* Increased logo container size */
            background: #4a5568;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Adjust the image size within the logo container */
        .logo-container img {
            height: 24px; /* Adjusted image size */
            width: 24px; /* Adjusted image size */
            object-fit: contain;
        }

        .profile-icon {
            width: 48px; /* Increased profile icon size */
            height: 48px; /* Increased profile icon size */
            background: #4a5568;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 18px; /* Adjusted font size for initial */
        }

        .sidebar-nav {
            height: calc(100vh - 200px);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #4a5568 #2d3748;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: #2d3748;
            border-radius: 2px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: #4a5568;
            border-radius: 2px;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <aside class="sidebar text-white w-64 min-h-screen print:hidden">
            <div class="p-4 border-b border-gray-600">
                <div class="flex items-center justify-center">
                    <div class="logo-container">
                        <img src="{{ asset('storage/logos/mkdev.png') }}" alt="Logo" class="h-8 w-8 object-contain">
                    </div>
                </div>
                <div class="text-center mt-2">
                    <h1 class="text-lg font-semibold text-white">Business Gateway</h1>
                    <p class="text-gray-300 text-xs">Point of Sale System</p>
                </div>
            </div>

            <nav class="sidebar-nav mt-4 px-4">
                <a href="/dashboard"
                    class="nav-item flex items-center {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <div class="nav-icon-container">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                    </div>
                    <span class="font-medium">{{ __('Dashboard') }}</span>
                </a>

                @if (auth()->user()->role === 'admin')
                    <div class="mb-4">
                        <h3 class="text-gray-300 text-xs uppercase font-semibold mb-2 px-4">Administration</h3>
                        <a href="{{ route('categories.index') }}"
                            class="nav-item flex items-center {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <div class="nav-icon-container">
                                <i class="fas fa-tags nav-icon"></i>
                            </div>
                            <span class="font-medium">{{ __('Categories') }}</span>
                        </a>
                        <a href="{{ route('products.index') }}"
                            class="nav-item flex items-center {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <div class="nav-icon-container">
                                <i class="fas fa-box nav-icon"></i>
                            </div>
                            <span class="font-medium">{{ __('Products') }}</span>
                        </a>
                        <a href="/admin/orders"
                            class="nav-item flex items-center {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                            <div class="nav-icon-container">
                                <i class="fas fa-chart-line nav-icon"></i>
                            </div>
                            <span class="font-medium">{{ __('Sales') }}</span>
                        </a>
                        <a href="{{ route('users.index') }}"
                            class="nav-item flex items-center {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <div class="nav-icon-container">
                                <i class="fas fa-users nav-icon"></i>
                            </div>
                            <span class="font-medium">{{ __('Users') }}</span>
                        </a>
                    </div>
                @elseif(auth()->user()->role === 'clerk')
                    <div class="mb-4">
                        <h3 class="text-gray-300 text-xs uppercase font-semibold mb-2 px-4">Sales Operations</h3>
                        <a href="{{ route('pos.index') }}"
                            class="nav-item flex items-center {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                            <div class="nav-icon-container">
                                <i class="fas fa-cash-register nav-icon"></i>
                            </div>
                            <span class="font-medium">{{ __('Point of Sale') }}</span>
                        </a>
                        <a href="{{ route('orders.index') }}"
                            class="nav-item flex items-center {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                            <div class="nav-icon-container">
                                <i class="fas fa-receipt nav-icon"></i>
                            </div>
                            <span class="font-medium">{{ __('Sales') }}</span>
                        </a>
                    </div>
                @endif
            </nav>

            <div class="absolute bottom-0 w-64 border-t border-gray-600">
                <div class="p-4">
                    <div class="bg-gray-700 rounded-lg p-3">
                        <div class="flex items-center mb-3">
                            <div class="profile-icon">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="ml-3">
                                <div class="font-semibold text-white text-sm">{{ auth()->user()->name }}</div>
                                <div class="text-gray-300 text-xs capitalize">{{ auth()->user()->role }}</div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-center px-4 py-2 bg-red-500 text-white rounded-md">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="md:hidden p-4 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <button class="p-2 rounded-md bg-gray-700 text-white">
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
                    @if (session('status') === 'login' && !session()->has('welcome_message_displayed'))
                        <div class="mb-6">
                            <div class="bg-white rounded-lg shadow-md p-6">
                                <h1 class="text-xl font-bold text-gray-800 mb-2">Welcome back, {{ auth()->user()->name }}!</h1>
                                <p class="text-gray-600 text-sm">Here's what's happening with your business today.</p>
                            </div>
                        </div>
                        {{ session()->flash('welcome_message_displayed', true) }}
                    @endif
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>