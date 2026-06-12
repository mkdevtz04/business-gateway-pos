@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            {{ $isManager ? 'Sales & Orders' : 'My Orders' }}
        </h1>
        <p class="page-subtitle">
            {{ $isManager ? 'All transactions across every clerk' : 'Your transaction history' }}
        </p>
    </div>
    @if($isManager)
    <a href="{{ route('reports.index') }}" class="btn btn-dark">
        <i class="fas fa-chart-bar text-xs"></i> Full Reports
    </a>
    @endif
</div>

{{-- Admin/Owner: quick stats + filters --}}
@if($isManager)
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-dollar-sign text-blue-500"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-medium">Today's Revenue</p>
            <p class="text-xl font-extrabold text-gray-900">${{ number_format($todayRevenue, 2) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-shopping-cart text-emerald-500"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-medium">Orders Today</p>
            <p class="text-xl font-extrabold text-gray-900">{{ $todayOrders }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-calendar-week text-violet-500"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-medium">This Week</p>
            <p class="text-xl font-extrabold text-gray-900">${{ number_format($weekRevenue, 2) }}</p>
        </div>
    </div>
</div>

{{-- Filter bar --}}
<form method="GET" class="bg-white rounded-2xl border border-gray-200 shadow-sm px-5 py-4 mb-5 flex flex-wrap items-end gap-3">
    <div>
        <label class="form-label text-xs mb-1">Clerk</label>
        <select name="clerk_id" class="form-input w-40 text-sm">
            <option value="">All Clerks</option>
            @foreach($clerks as $clerk)
                <option value="{{ $clerk->id }}" {{ request('clerk_id') == $clerk->id ? 'selected' : '' }}>
                    {{ $clerk->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label text-xs mb-1">Payment</label>
        <select name="payment" class="form-input w-32 text-sm">
            <option value="">All</option>
            <option value="cash"   {{ request('payment') === 'cash'   ? 'selected' : '' }}>Cash</option>
            <option value="credit" {{ request('payment') === 'credit' ? 'selected' : '' }}>Credit</option>
        </select>
    </div>
    <div>
        <label class="form-label text-xs mb-1">From</label>
        <input type="date" name="from" value="{{ request('from') }}" class="form-input w-36 text-sm">
    </div>
    <div>
        <label class="form-label text-xs mb-1">To</label>
        <input type="date" name="to" value="{{ request('to') }}" class="form-input w-36 text-sm">
    </div>
    <button type="submit" class="btn btn-primary btn-sm self-end">
        <i class="fas fa-filter text-xs"></i> Filter
    </button>
    @if(request()->hasAny(['clerk_id','payment','from','to']))
        <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm self-end">Clear</a>
    @endif
</form>
@endif

{{-- Orders table --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center gap-3">
        <div class="search-wrapper flex-1 min-w-0 max-w-xs">
            <i class="fas fa-search search-icon"></i>
            <input id="table-search" type="text" class="form-input bg-gray-50" placeholder="Search orders…">
        </div>
        <span id="row-count" class="text-sm text-gray-400 ml-auto">{{ $orders->total() }} orders</span>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table" id="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    @if($isManager)
                    <th>Clerk</th>
                    @endif
                    <th>Customer</th>
                    <th>Payment</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr data-search="{{ strtolower('#'.$order->id.' '.($order->user?->name ?? '').' '.($order->customer?->name ?? 'walk-in')) }}">
                    <td class="font-mono font-semibold text-gray-700">
                        #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                    </td>

                    @if($isManager)
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($order->user?->name ?? 'U', 0, 2)) }}
                            </div>
                            <span class="text-sm text-gray-700">{{ $order->user?->name ?? '—' }}</span>
                        </div>
                    </td>
                    @endif

                    <td class="text-gray-700">{{ $order->customer?->name ?? 'Walk-in' }}</td>

                    <td>
                        @if($order->payment_method === 'cash')
                            <span class="badge badge-green"><i class="fas fa-money-bill-wave mr-1 text-xs"></i>Cash</span>
                        @else
                            <span class="badge badge-blue"><i class="fas fa-credit-card mr-1 text-xs"></i>Credit</span>
                        @endif
                    </td>

                    <td class="font-semibold text-gray-900">${{ number_format($order->total_amount, 2) }}</td>

                    <td><span class="status-instock">{{ ucfirst($order->status) }}</span></td>

                    <td class="text-gray-500 text-sm">
                        <div>{{ $order->created_at->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-300">{{ $order->created_at->format('H:i') }}</div>
                    </td>

                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('orders.show', $order) }}"
                               class="w-8 h-8 rounded-lg hover:bg-blue-50 flex items-center justify-center transition-colors text-blue-600"
                               title="View">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="{{ route('orders.pdf', $order) }}"
                               class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors text-gray-500"
                               title="Download PDF">
                                <i class="fas fa-file-pdf text-xs"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $isManager ? 8 : 7 }}" class="text-center py-14 text-gray-400">
                        <i class="fas fa-receipt text-3xl mb-3 block text-gray-200"></i>
                        No orders found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $orders->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
(function () {
    const search  = document.getElementById('table-search');
    const rows    = document.querySelectorAll('#orders-table tbody tr[data-search]');
    const countEl = document.getElementById('row-count');

    function update() {
        const q = search.value.trim().toLowerCase();
        let visible = 0;
        rows.forEach(r => {
            const show = !q || r.dataset.search.includes(q);
            r.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        if (q) countEl.textContent = `${visible} shown`;
    }
    search.addEventListener('input', update);
})();
</script>
@endpush
@endsection
