@extends('layouts.app')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Reports</h1>
        <p class="page-subtitle">Revenue &amp; performance analytics</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('reports.export.orders', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
           class="btn btn-secondary">
            <i class="fas fa-download text-xs"></i> Orders CSV
        </a>
        <a href="{{ route('reports.export.products', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
           class="btn btn-dark">
            <i class="fas fa-download text-xs"></i> Products CSV
        </a>
    </div>
</div>

{{-- Date Range Filter --}}
<form method="GET" class="bg-white rounded-2xl border border-gray-200 shadow-sm px-5 py-4 mb-6 flex flex-wrap items-end gap-4">
    <div>
        <label class="form-label text-xs mb-1">From</label>
        <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-input w-auto text-sm">
    </div>
    <div>
        <label class="form-label text-xs mb-1">To</label>
        <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-input w-auto text-sm">
    </div>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-filter text-xs"></i> Apply
    </button>
    {{-- Quick ranges --}}
    <div class="flex gap-2 ml-auto">
        @php
            $ranges = [
                'Today'      => [today()->format('Y-m-d'),                  today()->format('Y-m-d')],
                'This week'  => [now()->startOfWeek()->format('Y-m-d'),      now()->format('Y-m-d')],
                'This month' => [now()->startOfMonth()->format('Y-m-d'),     now()->format('Y-m-d')],
                'Last month' => [now()->subMonth()->startOfMonth()->format('Y-m-d'), now()->subMonth()->endOfMonth()->format('Y-m-d')],
            ];
        @endphp
        @foreach($ranges as $label => [$rf, $rt])
            <a href="{{ route('reports.index', ['from' => $rf, 'to' => $rt]) }}"
               class="text-xs px-3 py-1.5 rounded-lg border transition-colors
                      {{ $from->format('Y-m-d') === $rf && $to->format('Y-m-d') === $rt
                          ? 'bg-blue-600 text-white border-blue-600'
                          : 'bg-white text-gray-600 border-gray-200 hover:border-blue-400 hover:text-blue-600' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</form>

{{-- KPI Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-5 shadow-sm">
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
        <p class="text-2xl font-extrabold text-gray-900 mt-0.5">${{ number_format($totalRevenue, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">vs previous period</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mb-3">
            <i class="fas fa-shopping-cart text-emerald-500"></i>
        </div>
        <p class="text-sm text-gray-500 font-medium">Total Orders</p>
        <p class="text-2xl font-extrabold text-gray-900 mt-0.5">{{ number_format($totalOrders) }}</p>
        <p class="text-xs text-gray-400 mt-1">In selected period</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center mb-3">
            <i class="fas fa-chart-bar text-violet-500"></i>
        </div>
        <p class="text-sm text-gray-500 font-medium">Avg. Order Value</p>
        <p class="text-2xl font-extrabold text-gray-900 mt-0.5">${{ number_format($avgOrderValue, 2) }}</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center mb-3">
            <i class="fas fa-box text-orange-500"></i>
        </div>
        <p class="text-sm text-gray-500 font-medium">Units Sold</p>
        <p class="text-2xl font-extrabold text-gray-900 mt-0.5">{{ number_format($totalUnits) }}</p>
    </div>
</div>

{{-- Revenue Chart + Payment breakdown --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
    <div class="chart-card lg:col-span-2">
        <div class="chart-card-header flex items-center justify-between">
            <h3 class="chart-card-title"><i class="fas fa-chart-area"></i> Daily Revenue</h3>
            <span class="text-xs text-gray-400">{{ $from->format('M d') }} – {{ $to->format('M d, Y') }}</span>
        </div>
        <div class="chart-card-body">
            <div style="position:relative; height:180px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
    <div class="chart-card">
        <div class="chart-card-header">
            <h3 class="chart-card-title"><i class="fas fa-credit-card"></i> Payment Methods</h3>
        </div>
        <div class="chart-card-body">
            <div style="position:relative; height:180px;">
                <canvas id="paymentChart"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                @foreach($byPayment as $p)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 capitalize">{{ $p->payment_method }}</span>
                    <span class="font-semibold text-gray-900">${{ number_format($p->revenue, 2) }}
                        <span class="text-gray-400 font-normal">({{ $p->count }})</span>
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Top Products + Top Clerks --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
    <div class="chart-card">
        <div class="chart-card-header">
            <h3 class="chart-card-title"><i class="fas fa-star"></i> Top Products</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th class="text-right">Units</th>
                        <th class="text-right">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $i => $p)
                    <tr>
                        <td class="text-gray-400 font-medium w-8">{{ $i + 1 }}</td>
                        <td class="font-medium text-gray-900">{{ $p->name }}</td>
                        <td class="text-right text-gray-500">{{ number_format($p->qty) }}</td>
                        <td class="text-right font-semibold text-gray-900">${{ number_format($p->revenue, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-6 text-gray-400">No data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-card-header">
            <h3 class="chart-card-title"><i class="fas fa-user-tie"></i> Top Clerks</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Clerk</th>
                        <th class="text-right">Orders</th>
                        <th class="text-right">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topClerks as $clerk)
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr($clerk->user?->name ?? '?', 0, 2)) }}
                                </div>
                                <span class="font-medium text-gray-900">{{ $clerk->user?->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td class="text-right text-gray-500">{{ $clerk->order_count }}</td>
                        <td class="text-right font-semibold text-gray-900">${{ number_format($clerk->revenue, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center py-6 text-gray-400">No data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Orders Table --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
            <i class="fas fa-list text-gray-400 text-sm"></i> Orders in Period
        </h3>
        <span class="text-sm text-gray-400">{{ $totalOrders }} orders</span>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Clerk</th>
                    <th>Payment</th>
                    <th class="text-right">Total</th>
                    <th>Date</th>
                    <th class="text-center">View</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders->take(50) as $order)
                <tr>
                    <td class="font-mono font-semibold text-gray-700">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="text-gray-600">{{ $order->customer?->name ?? 'Walk-in' }}</td>
                    <td class="text-gray-600">{{ $order->user?->name ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $order->payment_method === 'cash' ? 'badge-green' : 'badge-blue' }}">
                            {{ ucfirst($order->payment_method) }}
                        </span>
                    </td>
                    <td class="text-right font-semibold">${{ number_format($order->total_amount, 2) }}</td>
                    <td class="text-gray-400 text-xs">{{ $order->created_at->format('M d, Y  H:i') }}</td>
                    <td class="text-center">
                        <a href="#" class="text-blue-600 hover:text-blue-800 text-xs">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-10 text-gray-400">No orders in this period</td></tr>
                @endforelse
                @if($orders->count() > 50)
                <tr>
                    <td colspan="7" class="text-center py-4 text-sm text-gray-400">
                        Showing 50 of {{ $orders->count() }}. Export CSV for the full list.
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const gridColor  = '#F3F4F6';
    const labelColor = '#9CA3AF';
    const scaleOpts  = { grid: { color: gridColor }, ticks: { color: labelColor, font: { size: 11 } } };

    const daily = @json($dailyRevenue);
    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: daily.map(d => d.date),
            datasets: [{
                data: daily.map(d => d.revenue),
                backgroundColor: 'rgba(37,99,235,0.8)',
                borderRadius: 5, borderSkipped: false
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

    const payments = @json($byPayment);
    new Chart(document.getElementById('paymentChart'), {
        type: 'doughnut',
        data: {
            labels: payments.map(p => p.payment_method.charAt(0).toUpperCase() + p.payment_method.slice(1)),
            datasets: [{ data: payments.map(p => p.revenue), backgroundColor: ['#2563EB', '#059669'], borderWidth: 3, borderColor: '#fff' }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endpush
@endsection
