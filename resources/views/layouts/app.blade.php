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
    @stack('styles')

    {{-- Dark mode flash prevention — runs before anything renders --}}
    <script>if (localStorage.getItem('darkMode') === 'true') document.documentElement.classList.add('dark');</script>

    <style>
        * { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        /* ── Sidebar nav links ─────────────────────── */
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 14px; border-radius: 9px;
            color: #6B7280; font-size: 0.875rem; font-weight: 500;
            text-decoration: none; transition: all 0.15s ease;
        }
        .sidebar-link:hover { background: #E5E7EB; color: #111827; }
        .sidebar-link.active { background: #2563EB; color: #fff; font-weight: 600; }
        .sidebar-link.active i { color: #fff; }
        .sidebar-icon { width: 16px; text-align: center; flex-shrink: 0; }

        /* ── Scrollbars ───────────────────────────── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #9CA3AF; }

        /* ── Utility card ─────────────────────────── */
        .card-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-lift:hover { transform: translateY(-2px); box-shadow: 0 8px 25px -5px rgba(0,0,0,0.1); }

        /* ── Page header ──────────────────────────── */
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
        .page-title  { font-size: 1.375rem; font-weight: 700; color: #111827; }
        .page-subtitle { font-size: 0.875rem; color: #6B7280; margin-top: 2px; }

        /* ── Data table ───────────────────────────── */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead th {
            padding: 10px 16px; text-align: left;
            font-size: 0.75rem; font-weight: 600; color: #6B7280;
            text-transform: uppercase; letter-spacing: 0.05em;
            background: #F9FAFB; border-bottom: 1px solid #E5E7EB;
        }
        .data-table tbody tr { border-bottom: 1px solid #F3F4F6; transition: background 0.1s; }
        .data-table tbody tr:last-child { border-bottom: none; }
        .data-table tbody tr:hover { background: #F9FAFB; }
        .data-table tbody td { padding: 12px 16px; font-size: 0.875rem; color: #374151; vertical-align: middle; }

        /* ── Buttons ──────────────────────────────── */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 0.875rem; font-weight: 500; cursor: pointer; transition: all 0.15s; border: none; text-decoration: none; }
        .btn-primary   { background: #2563EB; color: white; }
        .btn-primary:hover { background: #1D4ED8; color: white; }
        .btn-secondary { background: #F3F4F6; color: #374151; }
        .btn-secondary:hover { background: #E5E7EB; color: #111827; }
        .btn-danger    { background: #FEE2E2; color: #DC2626; }
        .btn-danger:hover { background: #FECACA; color: #B91C1C; }
        .btn-dark      { background: #1E293B; color: white; }
        .btn-dark:hover { background: #0F172A; color: white; }
        .btn-sm  { padding: 5px 10px; font-size: 0.8125rem; }
        .btn-icon { padding: 7px; border-radius: 7px; }

        /* ── Badges ───────────────────────────────── */
        .badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 99px; font-size: 0.75rem; font-weight: 600; }
        .badge-green  { background: #D1FAE5; color: #065F46; }
        .badge-red    { background: #FEE2E2; color: #991B1B; }
        .badge-blue   { background: #DBEAFE; color: #1E40AF; }
        .badge-amber  { background: #FEF3C7; color: #92400E; }
        .badge-gray   { background: #F3F4F6; color: #4B5563; }
        .badge-purple { background: #EDE9FE; color: #5B21B6; }

        /* Status badges */
        .status-instock  { background: #1E293B; color: #fff; padding: 3px 10px; border-radius: 99px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; }
        .status-lowstock { background: transparent; color: #92400E; border: 1.5px solid #FCD34D; padding: 2px 9px; border-radius: 99px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; }
        .status-outstock { background: #EF4444; color: #fff; padding: 3px 10px; border-radius: 99px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; }

        /* ── Form fields ──────────────────────────── */
        .form-group  { margin-bottom: 1rem; }
        .form-label  { display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 6px; }
        .form-input  {
            width: 100%; padding: 9px 12px; border: 1.5px solid #E5E7EB; border-radius: 8px;
            font-size: 0.875rem; color: #111827; background: white;
            transition: border-color 0.15s, box-shadow 0.15s; outline: none;
        }
        .form-input:focus { border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .form-input::placeholder { color: #9CA3AF; }
        .form-error { font-size: 0.8125rem; color: #DC2626; margin-top: 4px; }

        /* ── Search bar ───────────────────────────── */
        .search-wrapper { position: relative; }
        .search-wrapper .search-icon { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #9CA3AF; font-size: 0.875rem; }
        .search-wrapper input { padding-left: 34px; }

        /* ── Stat / Chart card ────────────────────── */
        .stat-card { background: white; border-radius: 14px; padding: 20px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
        .stat-card-value { font-size: 1.875rem; font-weight: 800; color: #111827; line-height: 1; margin: 4px 0; }
        .stat-card-label { font-size: 0.8125rem; font-weight: 500; color: #6B7280; }
        .stat-card-sub   { font-size: 0.8125rem; color: #9CA3AF; margin-top: 6px; }

        .chart-card { background: white; border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); overflow: hidden; }
        .chart-card-header { padding: 16px 20px; border-bottom: 1px solid #F3F4F6; }
        .chart-card-title  { font-size: 0.9375rem; font-weight: 600; color: #111827; display: flex; align-items: center; gap: 8px; }
        .chart-card-title i { color: #6B7280; font-size: 0.875rem; }
        .chart-card-body   { padding: 20px; }

        /* ── Dark mode overrides ──────────────────── */
        html.dark body                    { background-color: #0F172A !important; }
        html.dark aside                   { background-color: #1E293B !important; border-color: #334155 !important; }
        html.dark header.top-bar          { background-color: #1E293B !important; border-color: #334155 !important; }
        html.dark .bg-white               { background-color: #1E293B !important; }
        html.dark .bg-gray-50             { background-color: #1E293B !important; }
        html.dark .bg-gray-100            { background-color: #0F172A !important; }
        html.dark .bg-gray-200            { background-color: #334155 !important; }
        html.dark .border-gray-100,
        html.dark .border-gray-200        { border-color: #334155 !important; }
        html.dark .text-gray-900          { color: #F1F5F9 !important; }
        html.dark .text-gray-800          { color: #E2E8F0 !important; }
        html.dark .text-gray-700          { color: #CBD5E1 !important; }
        html.dark .text-gray-600          { color: #94A3B8 !important; }
        html.dark .text-gray-500          { color: #64748B !important; }
        html.dark .text-gray-400          { color: #475569 !important; }
        html.dark .page-title             { color: #F1F5F9 !important; }
        html.dark .page-subtitle          { color: #94A3B8 !important; }
        html.dark .sidebar-link           { color: #94A3B8 !important; }
        html.dark .sidebar-link:hover     { background-color: #334155 !important; color: #F1F5F9 !important; }
        html.dark .sidebar-link.active    { background-color: #2563EB !important; color: #fff !important; }
        html.dark .data-table thead th    { background-color: #0F172A !important; color: #64748B !important; border-color: #334155 !important; }
        html.dark .data-table tbody tr    { border-color: #334155 !important; }
        html.dark .data-table tbody tr:hover { background-color: #334155 !important; }
        html.dark .data-table tbody td    { color: #CBD5E1 !important; }
        html.dark .chart-card,
        html.dark .stat-card              { background-color: #1E293B !important; }
        html.dark .chart-card-header      { border-color: #334155 !important; }
        html.dark .chart-card-title       { color: #F1F5F9 !important; }
        html.dark .form-input             { background-color: #334155 !important; border-color: #475569 !important; color: #F1F5F9 !important; }
        html.dark .form-input::placeholder { color: #475569 !important; }
        html.dark .form-label             { color: #CBD5E1 !important; }
        html.dark .btn-secondary          { background-color: #334155 !important; color: #CBD5E1 !important; }
        html.dark .btn-secondary:hover    { background-color: #475569 !important; color: #F1F5F9 !important; }
        html.dark .badge-gray             { background-color: #334155 !important; color: #94A3B8 !important; }
        html.dark .badge-blue             { background-color: rgba(37,99,235,0.2) !important; color: #93C5FD !important; }
        html.dark select option           { background-color: #1E293B; color: #F1F5F9; }
        html.dark .rounded-2xl.border,
        html.dark .rounded-2xl.border-gray-200 { border-color: #334155 !important; }
    </style>
</head>

<body class="bg-gray-100 antialiased"
      x-data="{
          sidebarOpen: false,
          darkMode: localStorage.getItem('darkMode') === 'true',
          toggleDark() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('darkMode', this.darkMode);
              document.documentElement.classList.toggle('dark', this.darkMode);
          }
      }">

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" x-cloak
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/40 z-20 lg:hidden"></div>

    <div class="flex h-screen overflow-hidden">

        {{-- ======== SIDEBAR ======== --}}
        @php
            $stockAlerts = 0;
            if (in_array(auth()->user()->role, ['admin', 'owner'])) {
                $lowStockCount = \App\Models\Product::where('quantity_available', '>', 0)
                    ->where('quantity_available', '<', 10)->count();
                $outStockCount = \App\Models\Product::where('quantity_available', '<=', 0)->count();
                $stockAlerts   = $lowStockCount + $outStockCount;
            }
        @endphp

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed lg:static lg:translate-x-0 z-30 w-60 h-screen bg-gray-100 border-r border-gray-200 flex flex-col transition-transform duration-300 ease-in-out print:hidden flex-shrink-0">

            {{-- Brand --}}
            <div class="flex items-center gap-2.5 px-5 py-5 border-b border-gray-200">
                <div class="w-8 h-8 rounded-lg overflow-hidden flex-shrink-0">
                    <img src="{{ asset('storage/logos/mkdev.png') }}" alt="Logo"
                         class="w-full h-full object-contain"
                         onerror="this.parentElement.classList.add('bg-blue-600'); this.style.display='none'">
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-900 leading-tight">Business Gateway</div>
                    <div class="text-xs text-gray-400">Point of Sale</div>
                </div>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
                <a href="/dashboard"
                   class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie sidebar-icon"></i>
                    <span>Dashboard</span>
                </a>

                @php $role = auth()->user()->role; @endphp

                @if($role === 'admin' || $role === 'owner')
                <div class="pt-5 pb-2 px-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        {{ $role === 'owner' ? 'Business' : 'Administration' }}
                    </p>
                </div>

                <a href="{{ route('orders.index') }}"
                   class="sidebar-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                    <i class="fas fa-receipt sidebar-icon"></i>
                    <span>Sales & Orders</span>
                </a>

                @if($role === 'admin')
                <a href="{{ route('customers.index') }}"
                   class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="fas fa-users sidebar-icon"></i>
                    <span>Customers</span>
                </a>

                <a href="{{ route('categories.index') }}"
                   class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags sidebar-icon"></i>
                    <span>Categories</span>
                </a>

                <a href="{{ route('products.index') }}"
                   class="sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-box sidebar-icon"></i>
                    <span class="flex-1">Products</span>
                    @if($stockAlerts > 0)
                        <span class="text-xs bg-amber-400 text-amber-900 font-bold px-1.5 py-0.5 rounded-full leading-none">
                            {{ $stockAlerts }}
                        </span>
                    @endif
                </a>
                @endif

                <a href="{{ route('stocks.index') }}"
                   class="sidebar-link {{ request()->routeIs('stocks.*') ? 'active' : '' }}">
                    <i class="fas fa-warehouse sidebar-icon"></i>
                    <span class="flex-1">Stock</span>
                    @if($stockAlerts > 0)
                        <span class="text-xs bg-amber-400 text-amber-900 font-bold px-1.5 py-0.5 rounded-full leading-none">
                            {{ $stockAlerts }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('reports.index') }}"
                   class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar sidebar-icon"></i>
                    <span>Reports</span>
                </a>

                @if($role === 'admin')
                <a href="{{ route('users.index') }}"
                   class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-user-shield sidebar-icon"></i>
                    <span>Users</span>
                </a>
                @endif

                @elseif($role === 'clerk')
                <div class="pt-5 pb-2 px-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Sales Operations</p>
                </div>
                <a href="{{ route('pos.index') }}"
                   class="sidebar-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                    <i class="fas fa-cash-register sidebar-icon"></i>
                    <span>Point of Sale</span>
                </a>
                <a href="{{ route('orders.index') }}"
                   class="sidebar-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                    <i class="fas fa-list-alt sidebar-icon"></i>
                    <span>My Orders</span>
                </a>
                @endif
            </nav>

            {{-- User + actions --}}
            <div class="border-t border-gray-200 p-3 space-y-1">
                {{-- User info --}}
                <div class="flex items-center gap-2.5 px-2 py-2">
                    <div class="w-8 h-8 rounded-full bg-gray-800 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-900 truncate leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                </div>
                {{-- Profile link --}}
                <a href="{{ route('profile.edit') }}"
                   class="sidebar-link text-sm py-2 {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="fas fa-user-circle sidebar-icon text-xs"></i>
                    <span>My Profile</span>
                </a>
                {{-- Sign out --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2 text-xs text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors font-medium">
                        <i class="fas fa-sign-out-alt text-xs"></i>
                        Sign Out
                    </button>
                </form>
            </div>
        </aside>

        {{-- ======== MAIN CONTENT ======== --}}
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">

            {{-- Top Header Bar --}}
            <header class="top-bar flex-shrink-0 bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between print:hidden">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true"
                            class="lg:hidden w-9 h-9 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-bars"></i>
                    </button>
                    <span class="hidden lg:block text-sm text-gray-400">
                        {{ now()->format('l, d M Y') }}
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    {{-- Stock alert bell (admin/owner only) --}}
                    @if($stockAlerts > 0 && in_array(auth()->user()->role, ['admin', 'owner']))
                    <a href="{{ route('stocks.index') }}"
                       class="relative w-9 h-9 rounded-full bg-amber-50 hover:bg-amber-100 flex items-center justify-center transition-colors"
                       title="{{ $stockAlerts }} stock alert(s)">
                        <i class="fas fa-bell text-amber-500 text-sm"></i>
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center leading-none">
                            {{ $stockAlerts > 9 ? '9+' : $stockAlerts }}
                        </span>
                    </a>
                    @endif

                    {{-- Dark mode toggle --}}
                    <button @click="toggleDark()"
                            class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors"
                            title="Toggle dark mode">
                        <i :class="darkMode ? 'fas fa-sun text-yellow-400' : 'fas fa-moon text-gray-500'" class="text-sm"></i>
                    </button>

                    {{-- User avatar --}}
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-gray-800 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="hidden sm:block">
                            <div class="text-sm font-semibold text-gray-900 leading-tight">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-gray-400">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto">
                {{-- Flash toasts --}}
                @if(session('success'))
                <script>document.addEventListener('DOMContentLoaded',()=>showToast(@json(session('success')),'success'))</script>
                @endif
                @if(session('error'))
                <script>document.addEventListener('DOMContentLoaded',()=>showToast(@json(session('error')),'error'))</script>
                @endif
                @if(session('warning'))
                <script>document.addEventListener('DOMContentLoaded',()=>showToast(@json(session('warning')),'warning'))</script>
                @endif
                @if(session('status') === 'profile-updated')
                <script>document.addEventListener('DOMContentLoaded',()=>showToast('Profile updated successfully','success'))</script>
                @endif

                <div class="p-6">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- Toast container --}}
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none" style="min-width:280px"></div>

    {{-- Confirm modal --}}
    <div id="confirm-modal" class="fixed inset-0 bg-black/50 z-50 items-center justify-center p-4 backdrop-blur-sm" style="display:none">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6">
            <div class="flex gap-4 mb-5">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Confirm Action</h3>
                    <p id="confirm-message" class="text-sm text-gray-500 mt-1">Are you sure?</p>
                </div>
            </div>
            <div class="flex gap-3 justify-end">
                <button id="confirm-cancel" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                <button id="confirm-ok"     class="px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">Delete</button>
            </div>
        </div>
    </div>

    @stack('scripts')
    @stack('modals')

    <script>
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;
        const cfg = {
            success: { bg: 'bg-emerald-500', icon: 'fa-check-circle' },
            error:   { bg: 'bg-red-500',     icon: 'fa-times-circle' },
            warning: { bg: 'bg-amber-500',   icon: 'fa-exclamation-circle' },
            info:    { bg: 'bg-blue-500',    icon: 'fa-info-circle' },
        };
        const c = cfg[type] || cfg.info;
        const toast = document.createElement('div');
        toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl text-white shadow-lg text-sm font-medium max-w-xs transform translate-y-3 opacity-0 transition-all duration-300 ${c.bg}`;
        toast.innerHTML = `<i class="fas ${c.icon} flex-shrink-0"></i><span>${message}</span>`;
        container.appendChild(toast);
        requestAnimationFrame(() => requestAnimationFrame(() => toast.classList.remove('translate-y-3', 'opacity-0')));
        setTimeout(() => {
            toast.classList.add('translate-y-3', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    function showConfirm(message, onConfirm, confirmLabel = 'Delete') {
        const modal     = document.getElementById('confirm-modal');
        const msgEl     = document.getElementById('confirm-message');
        const okBtn     = document.getElementById('confirm-ok');
        const cancelBtn = document.getElementById('confirm-cancel');
        msgEl.textContent  = message;
        okBtn.textContent  = confirmLabel;
        modal.style.display = 'flex';
        const close = () => { modal.style.display = 'none'; };
        const handleOk     = () => { close(); onConfirm(); cleanup(); };
        const handleCancel = () => { close(); cleanup(); };
        const handleBg     = (e) => { if (e.target === modal) { close(); cleanup(); } };
        function cleanup() {
            okBtn.removeEventListener('click', handleOk);
            cancelBtn.removeEventListener('click', handleCancel);
            modal.removeEventListener('click', handleBg);
        }
        okBtn.addEventListener('click', handleOk);
        cancelBtn.addEventListener('click', handleCancel);
        modal.addEventListener('click', handleBg);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[data-confirm]').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                showConfirm(this.dataset.confirm || 'Are you sure?', () => this.submit());
            });
        });
    });

    window.showToast   = showToast;
    window.showConfirm = showConfirm;
    </script>
</body>
</html>
