@extends('layouts.app')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
{{-- Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">My Sales Dashboard</h1>
        <p class="page-subtitle">Welcome back, {{ auth()->user()->name }} &mdash; {{ now()->format('l, F d, Y') }}</p>
    </div>
    <a href="{{ route('pos.index') }}" class="btn btn-primary">
        <i class="fas fa-cash-register"></i>
        New Sale
    </a>
</div>

{{-- KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card card-lift">
        <div class="flex items-center justify-between mb-2">
            <span class="stat-card-label">Today's Sales</span>
            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                <i class="fas fa-calendar-day text-blue-500 text-sm"></i>
            </div>
        </div>
        <div class="stat-card-value" data-count="{{ $todaySales }}" data-prefix="$" data-decimals="0">$0</div>
        <p class="stat-card-sub">{{ $todayOrders }} {{ Str::plural('order', $todayOrders) }} today</p>
    </div>

    <div class="stat-card card-lift">
        <div class="flex items-center justify-between mb-2">
            <span class="stat-card-label">Total Revenue</span>
            <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center">
                <i class="fas fa-dollar-sign text-emerald-500 text-sm"></i>
            </div>
        </div>
        <div class="stat-card-value" data-count="{{ $totalSales }}" data-prefix="$" data-decimals="0">$0</div>
        <p class="stat-card-sub">All-time earnings</p>
    </div>

    <div class="stat-card card-lift">
        <div class="flex items-center justify-between mb-2">
            <span class="stat-card-label">Total Orders</span>
            <div class="w-9 h-9 rounded-lg bg-violet-50 flex items-center justify-center">
                <i class="fas fa-shopping-cart text-violet-500 text-sm"></i>
            </div>
        </div>
        <div class="stat-card-value" data-count="{{ $totalOrders }}" data-prefix="" data-decimals="0">0</div>
        <p class="stat-card-sub">Orders processed</p>
    </div>

    <div class="stat-card card-lift">
        <div class="flex items-center justify-between mb-2">
            <span class="stat-card-label">Avg Order</span>
            <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center">
                <i class="fas fa-chart-line text-amber-500 text-sm"></i>
            </div>
        </div>
        <div class="stat-card-value" data-count="{{ $averageOrderValue }}" data-prefix="$" data-decimals="0">$0</div>
        <p class="stat-card-sub">Per transaction</p>
    </div>
</div>

{{-- Sales Trend + Category --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
    <div class="chart-card lg:col-span-2">
        <div class="chart-card-header">
            <h3 class="chart-card-title"><i class="fas fa-chart-area"></i> My Sales Trend — Last 14 Days</h3>
        </div>
        <div class="chart-card-body">
            <canvas id="personalTrendChart" height="110"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-card-header">
            <h3 class="chart-card-title"><i class="fas fa-chart-pie"></i> My Sales by Category</h3>
        </div>
        <div class="chart-card-body">
            <canvas id="myCategoryChart" height="220"></canvas>
        </div>
    </div>
</div>

{{-- Weekly + Top Products --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
    <div class="chart-card">
        <div class="chart-card-header">
            <h3 class="chart-card-title"><i class="fas fa-chart-bar"></i> Weekly Performance — Last 8 Weeks</h3>
        </div>
        <div class="chart-card-body">
            <canvas id="weeklyChart" height="150"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-card-header">
            <h3 class="chart-card-title"><i class="fas fa-star"></i> My Top Selling Products</h3>
        </div>
        <div class="chart-card-body">
            <canvas id="topProductsChart" height="150"></canvas>
        </div>
    </div>
</div>

{{-- Hourly --}}
<div class="chart-card">
    <div class="chart-card-header">
        <h3 class="chart-card-title"><i class="fas fa-clock"></i> My Hourly Pattern — Average Last 7 Days</h3>
    </div>
    <div class="chart-card-body">
        <canvas id="hourlyPatternChart" height="90"></canvas>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Animated counters ─────────────────────────────────────
    document.querySelectorAll('[data-count]').forEach(el => {
        const target = parseFloat(el.dataset.count) || 0;
        const prefix = el.dataset.prefix || '';
        const decimals = parseInt(el.dataset.decimals) || 0;
        const start = performance.now();
        const duration = 1100;
        function frame(now) {
            const p = Math.min((now - start) / duration, 1);
            const ease = 1 - Math.pow(1 - p, 3);
            el.textContent = prefix + (target * ease).toLocaleString('en-US', {
                minimumFractionDigits: decimals, maximumFractionDigits: decimals
            });
            if (p < 1) requestAnimationFrame(frame);
        }
        requestAnimationFrame(frame);
    });

    const gridColor  = '#F3F4F6';
    const labelColor = '#6B7280';
    const palette    = ['#2563EB','#7C3AED','#059669','#D97706','#DC2626','#0891B2','#65A30D','#9333EA'];

    const scaleOpts = {
        grid: { color: gridColor },
        ticks: { color: labelColor, font: { size: 11 } }
    };

    // ── Personal Sales Trend ──────────────────────────────────
    const trendData = @json($salesTrend ?? []);
    new Chart(document.getElementById('personalTrendChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: trendData.map(d => d.date),
            datasets: [{
                label: 'Revenue ($)',
                data: trendData.map(d => d.sales),
                borderColor: '#2563EB',
                backgroundColor: 'rgba(37,99,235,0.08)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 5,
                pointBackgroundColor: '#2563EB'
            }, {
                label: 'Orders',
                data: trendData.map(d => d.orders),
                borderColor: '#059669',
                backgroundColor: 'transparent',
                borderWidth: 2,
                borderDash: [5, 4],
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { usePointStyle: true, padding: 20, color: labelColor, font: { size: 11 } }
                }
            },
            scales: {
                x: { ...scaleOpts, grid: { display: false } },
                y: { ...scaleOpts, ticks: { ...scaleOpts.ticks, callback: v => '$' + v.toLocaleString() } },
                y1: {
                    ...scaleOpts,
                    position: 'right',
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });

    // ── Category Doughnut ─────────────────────────────────────
    const catData = @json($salesByCategory ?? []);
    new Chart(document.getElementById('myCategoryChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: catData.map(d => d.category_name),
            datasets: [{
                data: catData.map(d => d.total_sales),
                backgroundColor: palette,
                borderWidth: 3, borderColor: '#fff'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 14, usePointStyle: true, font: { size: 11 }, color: labelColor }
                }
            }
        }
    });

    // ── Weekly Bar ────────────────────────────────────────────
    const weekData = @json($weeklySales ?? []);
    new Chart(document.getElementById('weeklyChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: weekData.map(d => d.week),
            datasets: [{
                data: weekData.map(d => d.sales),
                backgroundColor: 'rgba(8,145,178,0.85)',
                borderRadius: 6, borderSkipped: false
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ...scaleOpts, grid: { display: false } },
                y: { ...scaleOpts, ticks: { ...scaleOpts.ticks, callback: v => '$' + v.toLocaleString() } }
            }
        }
    });

    // ── Top Products (horizontal) ─────────────────────────────
    const prodData = @json($topProducts ?? []);
    new Chart(document.getElementById('topProductsChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: prodData.map(d => d.name.length > 16 ? d.name.slice(0,16)+'…' : d.name),
            datasets: [{
                label: 'Qty Sold',
                data: prodData.map(d => d.total_quantity),
                backgroundColor: palette,
                borderRadius: 5, borderSkipped: false
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ...scaleOpts, beginAtZero: true },
                y: { ...scaleOpts, grid: { display: false } }
            }
        }
    });

    // ── Hourly Pattern ────────────────────────────────────────
    const hourData = @json($hourlyData ?? []);
    new Chart(document.getElementById('hourlyPatternChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: hourData.map(d => d.hour),
            datasets: [{
                data: hourData.map(d => d.avg_sales),
                borderColor: '#D97706',
                backgroundColor: 'rgba(217,119,6,0.08)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 5,
                pointBackgroundColor: '#D97706'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ...scaleOpts, grid: { display: false } },
                y: { ...scaleOpts, ticks: { ...scaleOpts.ticks, callback: v => '$' + v.toFixed(0) } }
            }
        }
    });
});
</script>
@endpush
@endsection
