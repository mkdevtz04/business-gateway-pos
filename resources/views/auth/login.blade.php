<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Welcome back</h2>
        <p class="text-gray-500 text-sm mt-1">Sign in to your account to continue</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                    <i class="fas fa-envelope"></i>
                </span>
                <input id="email" name="email" type="email"
                       value="{{ old('email') }}"
                       required autofocus autocomplete="username"
                       class="form-input pl-9 @error('email') border-red-400 @enderror"
                       placeholder="you@example.com">
            </div>
            @error('email')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                    <i class="fas fa-lock"></i>
                </span>
                <input id="password" name="password" type="password"
                       required autocomplete="current-password"
                       class="form-input pl-9 @error('password') border-red-400 @enderror"
                       placeholder="••••••••">
            </div>
            @error('password')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember + Forgot --}}
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input id="remember_me" name="remember" type="checkbox"
                       class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-600">Remember me</span>
            </label>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    Forgot password?
                </a>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors text-sm shadow-sm">
            <i class="fas fa-sign-in-alt"></i>
            Sign In
        </button>
    </form>
</x-guest-layout>
