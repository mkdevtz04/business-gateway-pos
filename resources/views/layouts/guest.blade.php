<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Business Gateway POS') }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #F8FAFC; }

        .auth-left {
            background: linear-gradient(135deg, #1E40AF 0%, #2563EB 50%, #3B82F6 100%);
            position: relative;
            overflow: hidden;
        }
        .auth-left::before {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            top: -100px; right: -100px;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            bottom: -80px; left: -80px;
        }

        .form-input {
            width: 100%; padding: 10px 14px; border: 1.5px solid #E5E7EB; border-radius: 9px;
            font-size: 0.875rem; color: #111827; background: white; outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-input:focus { border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .form-input::placeholder { color: #9CA3AF; }
    </style>
</head>
<body>
    <div class="min-h-screen flex">
        {{-- Left panel --}}
        <div class="auth-left hidden lg:flex w-5/12 flex-col items-center justify-center p-12 text-white">
            <div class="relative z-10 text-center max-w-sm">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-8">
                    <i class="fas fa-cash-register text-3xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold mb-3">Business Gateway</h1>
                <p class="text-blue-200 text-base leading-relaxed">
                    Your all-in-one Point of Sale system. Manage sales, inventory, and team performance from one place.
                </p>
                <div class="mt-10 grid grid-cols-2 gap-4 text-left">
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                        <i class="fas fa-chart-line text-blue-200 mb-2 block"></i>
                        <div class="text-sm font-semibold">Real-time Analytics</div>
                        <div class="text-xs text-blue-200 mt-1">Track performance as it happens</div>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                        <i class="fas fa-box text-blue-200 mb-2 block"></i>
                        <div class="text-sm font-semibold">Inventory Control</div>
                        <div class="text-xs text-blue-200 mt-1">Never run out of stock</div>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                        <i class="fas fa-users text-blue-200 mb-2 block"></i>
                        <div class="text-sm font-semibold">Team Management</div>
                        <div class="text-xs text-blue-200 mt-1">Role-based access control</div>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                        <i class="fas fa-file-invoice text-blue-200 mb-2 block"></i>
                        <div class="text-sm font-semibold">PDF Invoices</div>
                        <div class="text-xs text-blue-200 mt-1">Professional receipts instantly</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right panel --}}
        <div class="flex-1 flex flex-col items-center justify-center p-6 lg:p-12">
            <div class="w-full max-w-md">
                {{-- Mobile logo --}}
                <div class="lg:hidden flex items-center justify-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-cash-register text-white"></i>
                    </div>
                    <span class="text-lg font-bold text-gray-900">Business Gateway POS</span>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
