<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POSMaster - Your Ultimate Point of Sale Solution</title>
    <link href="https://fonts.bunny.net/css?family=inter:400,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-inter antialiased bg-gray-50">
    <!-- Hero Section -->
    <header class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="text-2xl font-bold">POSMaster</div>
            <div class="space-x-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="hover:text-gray-200 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-gray-200 transition">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="hover:text-gray-200 transition">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </nav>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <h1 class="text-4xl sm:text-5xl font-bold mb-4">Streamline Your Business with POSMaster</h1>
            <p class="text-lg sm:text-xl mb-8 max-w-2xl mx-auto">A powerful, Laravel-powered Point of Sale system designed to simplify sales, inventory, and customer management for businesses of all sizes.</p>
            <a href="{{ route('register') }}" class="inline-block bg-white text-blue-600 font-semibold py-3 px-6 rounded-lg hover:bg-gray-100 transition">Get Started Now</a>
        </div>
    </header>

    <!-- Features Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Why Choose POSMaster?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
                <div class="flex items-center justify-center h-12 w-12 bg-blue-100 text-blue-600 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5l6.75 6.75L21 9" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Fast Transactions</h3>
                <p class="text-gray-600">Process sales quickly and efficiently with our intuitive interface, reducing wait times for your customers.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
                <div class="flex items-center justify-center h-12 w-12 bg-blue-100 text-blue-600 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.75h-13.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h13.5a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Inventory Management</h3>
                <p class="text-gray-600">Track stock levels in real-time, manage products, and receive low-stock alerts to stay ahead.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
                <div class="flex items-center justify-center h-12 w-12 bg-blue-100 text-blue-600 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 18.75a7.5 7.5 0 0115 0" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Customer Insights</h3>
                <p class="text-gray-600">Understand your customers better with detailed sales reports and loyalty program integration.</p>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="bg-blue-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to Transform Your Business?</h2>
            <p class="text-lg mb-8 max-w-2xl mx-auto">Join thousands of businesses using POSMaster to streamline operations and boost sales.</p>
            <a href="{{ route('register') }}" class="inline-block bg-white text-blue-600 font-semibold py-3 px-6 rounded-lg hover:bg-gray-100 transition">Start Your Free Trial</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center">
            <div class="text-sm">&copy; {{ date('Y') }} POSMaster. All rights reserved.</div>
            <div class="mt-4 sm:mt-0 flex space-x-4">
                <a href="#" class="hover:text-white transition">Privacy Policy</a>
                <a href="#" class="hover:text-white transition">Terms of Service</a>
                <a href="#" class="hover:text-white transition">Contact Us</a>
            </div>
        </div>
    </footer>
</body>
</html>