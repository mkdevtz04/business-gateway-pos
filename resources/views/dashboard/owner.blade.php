@extends('layouts.app')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

{{-- Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Business Overview</h1>
        <p class="page-subtitle">{{ now()->format('l, F d, Y') }}</p>
    </div>
    <a href="{{ route('reports.index') }}" class="btn btn-dark">
        <i class="fas fa-chart-bar text-xs"></i> Full Reports
    </a>
</div>

{{-- ── KPI CARDS ── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

    {{-- Today's Revenue --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm card-lift">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <i class="fas fa-dollar-sign text-blue-500"></i>
            </div>
            <span class="text-xs font-semibold text-gray-400">Today</span>
        </div>
        <p class="text-sm text-gray-500 font-medium">Today's Revenue</p>
        <p class="text-2xl font-extrabold text-gray-900 mt-0.5">${{ number_format($todayRevenue, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $activeStaffToday }} clerk(s) active</p>
    </div>

    {{-- This Month --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm card-lift">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                <i class="fas fa-calendar-alt text-emerald-500"></i>
            </div>
            <span class="text-xs font-semibold {{ $revenueChange >= 0 ? 'text-emerald-600' : 'text-red-500' }} flex items-center gap-1">
                <i class="fas fa-arrow-{{ $revenueChange >= 0 ? 'up' : 'down' }} text-xs"></i>
                {{ number_format(abs($revenueChange), 1) }}%
            </span>
        </div>
        <p class="text-sm text-gray-500 font-medium">This Month</p>
        <p class="text-2xl font-extrabold text-gray-900 mt-0.5">${{ number_format($thisMonthRevenue, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">vs ${{ number_format($lastMonthRevenue, 2) }} last month</p>
    </div>

    {{-- Monthly Orders --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm card-lift">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center">
                <i class="fas fa-shopping-cart text-violet-500"></i>
            </div>
            <span class="text-xs font-semibold text-gray-400">{{ now()->format('M') }}</span>
        </div>
        <p class="text-sm text-gray-500 font-medium">Orders This Month</p>
        <p class="text-2xl font-extrabold text-gray-900 mt-0.5">{{ number_format($thisMonthOrders) }}</p>
        <p class="text-xs text-gray-400 mt-1">
            Avg ${{ $thisMonthOrders > 0 ? number_format($thisMonthRevenue / $thisMonthOrders, 2) : '0.00' }} / order
        </p>
    </div>

    {{-- All Time --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm card-lift">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center">
                <i class="fas fa-coins text-orange-500"></i>
            </div>
            <span class="text-xs font-semibold text-gray-400">All time</span>
        </div>
        <p class="text-sm text-gray-500 font-medium">Total Revenue</p>
        <p class="text-2xl font-extrabold text-gray-900 mt-0.5">${{ number_format($allTimeRevenue, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $totalStaff }} sales {{ Str::plural('clerk', $totalStaff) }}</p>
    </div>
</div>

{{-- ── CHARTS ROW ── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

    {{-- Revenue Trend --}}
    <div class="chart-card lg:col-span-2">
        <div class="chart-card-header flex items-center justify-between">
            <h3 class="chart-card-title"><i class="fas fa-chart-area"></i> Revenue Trend</h3>
            <span class="text-xs text-gray-400">Last 30 days</span>
        </div>
        <div class="chart-card-body">
            <div style="position:relative; height:180px;">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Payment Breakdown --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <h3 class="chart-card-title"><i class="fas fa-credit-card"></i> Payment Split</h3>
        </div>
        <div class="chart-card-body">
            <div style="position:relative; height:160px;">
                <canvas id="paymentChart"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                @foreach($paymentBreakdown as $p)
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas {{ $p->payment_method === 'cash' ? 'fa-money-bill-wave text-emerald-500' : 'fa-credit-card text-blue-500' }} text-xs"></i>
                        <span class="text-gray-600 capitalize">{{ $p->payment_method }}</span>
                    </div>
                    <div class="text-right">
                        <span class="font-semibold text-gray-900">${{ number_format($p->revenue, 2) }}</span>
                        <span class="text-gray-400 text-xs ml-1">({{ $p->count }})</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── STAFF PERFORMANCE ── --}}
<div class="chart-card mb-4">
    <div class="chart-card-header flex items-center justify-between">
        <h3 class="chart-card-title"><i class="fas fa-user-tie"></i> Staff Performance — {{ now()->format('F Y') }}</h3>
        <a href="{{ route('orders.index') }}" class="text-xs text-blue-600 hover:underline font-medium">All orders</a>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Clerk</th>
                    <th>Today Orders</th>
                    <th>Today Revenue</th>
                    <th>Month Orders</th>
                    <th>Month Revenue</th>
                    <th>Avg Order</th>
                    <th class="text-center">Activity</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staffPerformance as $i => $clerk)
                <tr>
                    <td class="text-gray-400 font-medium w-8 text-sm">{{ $i + 1 }}</td>
                    <td>
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0
                                {{ $i === 0 ? 'bg-yellow-100 text-yellow-700' : ($i === 1 ? 'bg-gray-100 text-gray-600' : 'bg-blue-50 text-blue-600') }}">
                                {{ strtoupper(substr($clerk->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $clerk->name }}</p>
                                @if($i === 0)
                                    <p class="text-xs text-yellow-600 font-medium">Top performer</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-blue">{{ $clerk->today_orders }}</span>
                    </td>
                    <td class="font-semibold {{ $clerk->today_revenue > 0 ? 'text-emerald-600' : 'text-gray-300' }}">
                        ${{ number_format($clerk->today_revenue, 2) }}
                    </td>
                    <td class="text-gray-600">{{ number_format($clerk->month_orders) }}</td>
                    <td class="font-semibold text-gray-900">${{ number_format($clerk->month_revenue, 2) }}</td>
                    <td class="text-gray-500">${{ number_format($clerk->avg_order, 2) }}</td>
                    <td class="text-center">
                        <a href="{{ route('orders.index', ['clerk_id' => $clerk->id]) }}"
                           class="text-xs text-blue-600 hover:underline font-medium">
                            View <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-10 text-gray-400">
                        <i class="fas fa-user-clock text-3xl mb-3 block text-gray-200"></i>
                        No clerk activity this month yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── STOCK HEALTH + RECENT ORDERS ── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Stock Health --}}
    <div class="chart-card">
        <div class="chart-card-header flex items-center justify-between">
            <h3 class="chart-card-title"><i class="fas fa-warehouse"></i> Stock Health</h3>
            <a href="{{ route('stocks.index') }}" class="text-xs text-blue-600 hover:underline font-medium">Manage</a>
        </div>
        <div class="chart-card-body space-y-3">
            {{-- In Stock --}}
            <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <i class="fas fa-check text-emerald-600 text-xs"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">In Stock</span>
                </div>
                <span class="text-lg font-extrabold text-emerald-700">{{ $stockHealth['in_stock'] }}</span>
            </div>
            {{-- Low Stock --}}
            <div class="flex items-center justify-between p-3 bg-amber-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                        <i class="fas fa-exclamation text-amber-600 text-xs"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Low Stock</span>
                </div>
                <span class="text-lg font-extrabold text-amber-700">{{ $stockHealth['low_stock'] }}</span>
            </div>
            {{-- Out of Stock --}}
            <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                        <i class="fas fa-times text-red-600 text-xs"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Out of Stock</span>
                </div>
                <span class="text-lg font-extrabold text-red-600">{{ $stockHealth['out_stock'] }}</span>
            </div>
            <div class="pt-2 border-t border-gray-100 flex justify-between text-sm">
                <span class="text-gray-400">Total products</span>
                <span class="font-semibold text-gray-700">{{ $stockHealth['total'] }}</span>
            </div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="chart-card lg:col-span-2">
        <div class="chart-card-header flex items-center justify-between">
            <h3 class="chart-card-title"><i class="fas fa-receipt"></i> Recent Orders</h3>
            <a href="{{ route('orders.index') }}" class="text-xs text-blue-600 hover:underline font-medium">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Clerk</th>
                        <th>Customer</th>
                        <th>Payment</th>
                        <th>Amount</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td class="font-mono font-semibold text-gray-700 text-xs">
                            #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($order->user?->name ?? 'U', 0, 2)) }}
                                </div>
                                <span class="text-xs text-gray-600">{{ $order->user?->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="text-sm text-gray-600">{{ $order->customer?->name ?? 'Walk-in' }}</td>
                        <td>
                            <span class="badge {{ $order->payment_method === 'cash' ? 'badge-green' : 'badge-blue' }} text-xs">
                                {{ ucfirst($order->payment_method) }}
                            </span>
                        </td>
                        <td class="font-semibold text-gray-900 text-sm">${{ number_format($order->total_amount, 2) }}</td>
                        <td class="text-gray-400 text-xs">{{ $order->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-8 text-gray-400">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const gridColor  = '#F3F4F6';
    const labelColor = '#9CA3AF';
    const scaleOpts  = { grid: { color: gridColor }, ticks: { color: labelColor, font: { size: 11 } } };

    // Revenue trend
    const trend = @json($revenueTrend);
    new Chart(document.getElementById('revenueTrendChart'), {
        type: 'line',
        data: {
            labels: trend.map(d => d.date),
            datasets: [{
                data: trend.map(d => d.amount),
                borderColor: '#2563EB',
                backgroundColor: 'rgba(37,99,235,0.07)',
                borderWidth: 2.5,
                fill: true, tension: 0.4,
                pointRadius: 0, pointHoverRadius: 5,
                pointBackgroundColor: '#2563EB'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: { legend: { display: false } },
            scales: {
                x: { ...scaleOpts, grid: { display: false } },
                y: { ...scaleOpts, ticks: { ...scaleOpts.ticks, callback: v => '$' + v.toLocaleString() } }
            }
        }
    });

    // Payment doughnut
    const payments = @json($paymentBreakdown);
    new Chart(document.getElementById('paymentChart'), {
        type: 'doughnut',
        data: {
            labels: payments.map(p => p.payment_method.charAt(0).toUpperCase() + p.payment_method.slice(1)),
            datasets: [{
                data: payments.map(p => p.revenue),
                backgroundColor: ['#059669', '#2563EB'],
                borderWidth: 3, borderColor: '#fff'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '68%',
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endpush
@endsection
