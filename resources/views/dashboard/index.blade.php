@extends('layouts.app')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
{{-- Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Welcome back! Here's what's happening with your store today.</p>
    </div>
</div>

{{-- ── KPI CARDS ── --}}
@php
    $todaySales = \App\Models\Order::whereDate('created_at', today())->sum('total_amount');
    $yesterdaySales = \App\Models\Order::whereDate('created_at', today()->subDay())->sum('total_amount');
    $revenueChange = $yesterdaySales > 0 ? (($totalSales - $yesterdaySales) / $yesterdaySales) * 100 : 0;

    $lastMonthOrders = \App\Models\Order::whereMonth('created_at', now()->subMonth()->month)->count();
    $ordersChange = $lastMonthOrders > 0 ? (($totalOrders - $lastMonthOrders) / $lastMonthOrders) * 100 : 0;
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

    {{-- Total Revenue --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm card-lift">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <i class="fas fa-dollar-sign text-blue-500"></i>
            </div>
            <span class="text-xs font-semibold {{ $revenueChange >= 0 ? 'text-emerald-600' : 'text-red-500' }} flex items-center gap-1">
                <i class="fas fa-arrow-{{ $revenueChange >= 0 ? 'up' : 'down' }} text-xs"></i>
                {{ number_format(abs($revenueChange), 1) }}%
            </span>
        </div>
        <p class="text-sm text-gray-500 font-medium">Total Revenue</p>
        <p class="text-2xl font-extrabold text-gray-900 mt-0.5" data-count="{{ $totalSales }}" data-prefix="$" data-decimals="0">$0</p>
        <p class="text-xs text-gray-400 mt-1">All-time earnings</p>
    </div>

    {{-- Total Orders --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm card-lift">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                <i class="fas fa-shopping-cart text-emerald-500"></i>
            </div>
            <span class="text-xs font-semibold {{ $ordersChange >= 0 ? 'text-emerald-600' : 'text-red-500' }} flex items-center gap-1">
                <i class="fas fa-arrow-{{ $ordersChange >= 0 ? 'up' : 'down' }} text-xs"></i>
                {{ number_format(abs($ordersChange), 1) }}%
            </span>
        </div>
        <p class="text-sm text-gray-500 font-medium">Total Orders</p>
        <p class="text-2xl font-extrabold text-gray-900 mt-0.5" data-count="{{ $totalOrders }}" data-prefix="" data-decimals="0">0</p>
        <p class="text-xs text-gray-400 mt-1">Orders processed</p>
    </div>

    {{-- Active Clerks --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm card-lift">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center">
                <i class="fas fa-users text-violet-500"></i>
            </div>
            <span class="text-xs font-semibold text-gray-400">Staff</span>
        </div>
        <p class="text-sm text-gray-500 font-medium">Active Clerks</p>
        <p class="text-2xl font-extrabold text-gray-900 mt-0.5" data-count="{{ $totalClerks }}" data-prefix="" data-decimals="0">0</p>
        <p class="text-xs text-gray-400 mt-1">Sales staff</p>
    </div>

    {{-- Products Sold --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm card-lift">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center">
                <i class="fas fa-box text-orange-500"></i>
            </div>
            <span class="text-xs font-semibold text-gray-400">Catalogue</span>
        </div>
        <p class="text-sm text-gray-500 font-medium">Products Sold</p>
        @php $totalQtySold = \App\Models\Order::join('order_items','orders.id','=','order_items.order_id')->sum('order_items.quantity'); @endphp
        <p class="text-2xl font-extrabold text-gray-900 mt-0.5" data-count="{{ $totalQtySold }}" data-prefix="" data-decimals="0">0</p>
        <p class="text-xs text-gray-400 mt-1">Total units sold</p>
    </div>
</div>

{{-- ── CHARTS ROW 1 ── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
    <div class="chart-card lg:col-span-2">
        <div class="chart-card-header flex items-center justify-between">
            <h3 class="chart-card-title"><i class="fas fa-chart-area"></i> Sales Overview</h3>
            <span class="text-xs text-gray-400">Last 30 days</span>
        </div>
        <div class="chart-card-body">
            <canvas id="salesTrendChart" height="105"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <div class="chart-card-header">
            <h3 class="chart-card-title"><i class="fas fa-chart-pie"></i> By Category</h3>
        </div>
        <div class="chart-card-body">
            <canvas id="categoryChart" height="210"></canvas>
        </div>
    </div>
</div>

{{-- ── CHARTS ROW 2 ── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
    <div class="chart-card">
        <div class="chart-card-header flex items-center justify-between">
            <h3 class="chart-card-title"><i class="fas fa-calendar-alt"></i> Orders This Week</h3>
        </div>
        <div class="chart-card-body">
            <canvas id="monthlySalesChart" height="150"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <div class="chart-card-header">
            <h3 class="chart-card-title"><i class="fas fa-clock"></i> Hourly Pattern (Today)</h3>
        </div>
        <div class="chart-card-body">
            <canvas id="hourlyChart" height="150"></canvas>
        </div>
    </div>
</div>

{{-- ── BOTTOM ROW: Recent Orders + Top Products ── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

    {{-- Recent Orders --}}
    @php
        $recentOrders = \App\Models\Order::with(['user','customer'])->latest()->limit(5)->get();
    @endphp
    <div class="chart-card lg:col-span-2">
        <div class="chart-card-header flex items-center justify-between">
            <h3 class="chart-card-title"><i class="fas fa-receipt"></i> Recent Orders</h3>
            <a href="{{ route('orders.index') }}" class="text-xs text-blue-600 hover:underline font-medium">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Clerk</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td class="font-mono font-semibold text-gray-700">#{{ str_pad($order->id,5,'0',STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($order->user?->name ?? 'U', 0, 2)) }}
                                </div>
                                <span class="text-xs text-gray-600">{{ $order->user?->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="text-gray-700">{{ $order->customer?->name ?? 'Walk-in' }}</td>
                        <td class="font-semibold">${{ number_format($order->total_amount,2) }}</td>
                        <td class="text-gray-400 text-xs">{{ $order->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-6 text-gray-400">No orders yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Products --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <h3 class="chart-card-title"><i class="fas fa-star"></i> Top Products</h3>
        </div>
        <div class="chart-card-body space-y-3">
            @foreach($topProducts->take(5) as $prod)
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $prod->name }}</p>
                    <p class="text-xs text-gray-400">{{ number_format($prod->total_quantity) }} sold</p>
                </div>
                <span class="text-sm font-bold text-gray-900 ml-3">${{ number_format($prod->total_revenue) }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── CLERK PERFORMANCE TABLE ── --}}
<div class="chart-card">
    <div class="chart-card-header flex items-center justify-between">
        <h3 class="chart-card-title"><i class="fas fa-user-tie"></i> Clerk Performance Today</h3>
        <a href="{{ route('orders.index') }}" class="text-xs text-blue-600 hover:underline font-medium">View all orders</a>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Clerk</th>
                    <th class="text-right">Today Orders</th>
                    <th class="text-right">Today Revenue</th>
                    <th class="text-right">All-Time Orders</th>
                    <th class="text-right">All-Time Revenue</th>
                    <th class="text-center">View</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clerkPerformance as $clerk)
                <tr>
                    <td>
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                {{ strtoupper(substr($clerk->name, 0, 2)) }}
                            </div>
                            <span class="font-semibold text-gray-900">{{ $clerk->name }}</span>
                        </div>
                    </td>
                    <td class="text-right">
                        <span class="badge badge-blue">{{ $clerk->today_orders }}</span>
                    </td>
                    <td class="text-right font-semibold {{ $clerk->today_revenue > 0 ? 'text-emerald-600' : 'text-gray-400' }}">
                        ${{ number_format($clerk->today_revenue, 2) }}
                    </td>
                    <td class="text-right text-gray-500">{{ number_format($clerk->total_orders) }}</td>
                    <td class="text-right text-gray-600 font-medium">${{ number_format($clerk->total_revenue, 2) }}</td>
                    <td class="text-center">
                        <a href="{{ route('orders.index', ['clerk_id' => $clerk->id]) }}"
                           class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                            Orders <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-400">No clerk activity yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Animated counters ─────────────────────────────────
    document.querySelectorAll('[data-count]').forEach(el => {
        const target   = parseFloat(el.dataset.count) || 0;
        const prefix   = el.dataset.prefix || '';
        const decimals = parseInt(el.dataset.decimals) || 0;
        const start    = performance.now();
        function frame(now) {
            const p    = Math.min((now - start) / 1200, 1);
            const ease = 1 - Math.pow(1 - p, 3);
            el.textContent = prefix + (target * ease).toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
            if (p < 1) requestAnimationFrame(frame);
        }
        requestAnimationFrame(frame);
    });

    const gridColor  = '#F3F4F6';
    const labelColor = '#9CA3AF';
    const scaleOpts  = { grid: { color: gridColor }, ticks: { color: labelColor, font: { size: 11 } } };
    const palette    = ['#2563EB','#7C3AED','#059669','#D97706','#DC2626','#0891B2','#65A30D','#9333EA','#EA580C'];

    // Sales Trend
    const trendData = @json($salesTrend ?? []);
    new Chart(document.getElementById('salesTrendChart'), {
        type: 'line',
        data: {
            labels: trendData.map(d => d.date),
            datasets: [{ data: trendData.map(d => d.sales), borderColor: '#2563EB',
                backgroundColor: 'rgba(37,99,235,0.07)', borderWidth: 2.5,
                fill: true, tension: 0.4, pointRadius: 0, pointHoverRadius: 5, pointBackgroundColor: '#2563EB' }]
        },
        options: { responsive: true, maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: { legend: { display: false } },
            scales: { x: { ...scaleOpts, grid: { display: false } },
                      y: { ...scaleOpts, ticks: { ...scaleOpts.ticks, callback: v => '$' + v.toLocaleString() } } } }
    });

    // Category
    const catData = @json($salesByCategory ?? []);
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: { labels: catData.map(d => d.category_name),
            datasets: [{ data: catData.map(d => d.total_sales), backgroundColor: palette, borderWidth: 3, borderColor: '#fff' }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: { legend: { position: 'bottom', labels: { padding: 14, usePointStyle: true, font: { size: 11 }, color: labelColor } } } }
    });

    // Monthly / weekly bar
    const monthData = @json($monthlySales ?? []);
    new Chart(document.getElementById('monthlySalesChart'), {
        type: 'bar',
        data: { labels: monthData.map(d => d.month),
            datasets: [{ data: monthData.map(d => d.sales), backgroundColor: 'rgba(37,99,235,0.8)', borderRadius: 6, borderSkipped: false }] },
        options: { responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { ...scaleOpts, grid: { display: false } },
                      y: { ...scaleOpts, ticks: { ...scaleOpts.ticks, callback: v => '$' + v.toLocaleString() } } } }
    });

    // Hourly
    const hourlyData = @json($hourlyData ?? []);
    new Chart(document.getElementById('hourlyChart'), {
        type: 'bar',
        data: { labels: hourlyData.map(d => d.hour),
            datasets: [{ data: hourlyData.map(d => d.sales), backgroundColor: 'rgba(8,145,178,0.75)', borderRadius: 4, borderSkipped: false }] },
        options: { responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { ...scaleOpts, grid: { display: false } },
                      y: { ...scaleOpts, ticks: { ...scaleOpts.ticks, callback: v => '$' + v } } } }
    });
});
</script>
@endpush
@endsection
