@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <a href="{{ route('customers.index') }}" class="text-sm text-gray-400 hover:text-blue-600 flex items-center gap-1 mb-1">
            <i class="fas fa-arrow-left text-xs"></i> Customers
        </a>
        <h1 class="page-title">{{ $customer->name }}</h1>
        <p class="page-subtitle">Customer profile &amp; order history</p>
    </div>
    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-secondary">
        <i class="fas fa-pencil-alt text-xs"></i> Edit
    </a>
</div>

{{-- Stats + info --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    {{-- Info card --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm flex items-start gap-4">
        <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-extrabold text-xl flex-shrink-0">
            {{ strtoupper(substr($customer->name, 0, 2)) }}
        </div>
        <div>
            <p class="text-base font-bold text-gray-900">{{ $customer->name }}</p>
            <p class="text-sm text-gray-400 mt-0.5">
                <i class="fas fa-phone text-xs mr-1"></i>{{ $customer->contact ?: 'No contact' }}
            </p>
            <p class="text-xs text-gray-300 mt-2">
                Customer since {{ $customer->created_at->format('M d, Y') }}
            </p>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mb-3">
            <i class="fas fa-dollar-sign text-emerald-500"></i>
        </div>
        <p class="text-sm text-gray-500 font-medium">Total Spent</p>
        <p class="text-2xl font-extrabold text-gray-900 mt-0.5">${{ number_format($totalSpent, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">Across {{ $orderCount }} orders</p>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center mb-3">
            <i class="fas fa-receipt text-violet-500"></i>
        </div>
        <p class="text-sm text-gray-500 font-medium">Avg. Order Value</p>
        <p class="text-2xl font-extrabold text-gray-900 mt-0.5">${{ number_format($avgOrder, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">Per transaction</p>
    </div>
</div>

{{-- Order history --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
            <i class="fas fa-receipt text-gray-400 text-sm"></i> Order History
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Clerk</th>
                    <th>Items</th>
                    <th>Payment</th>
                    <th class="text-right">Total</th>
                    <th>Date</th>
                    <th class="text-center">Invoice</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customer->orders->sortByDesc('created_at') as $order)
                <tr>
                    <td class="font-mono font-semibold text-gray-700">
                        #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="text-gray-600">{{ $order->user?->name ?? '—' }}</td>
                    <td>
                        <span class="badge badge-gray">{{ $order->items->sum('quantity') }} items</span>
                    </td>
                    <td>
                        <span class="badge {{ $order->payment_method === 'cash' ? 'badge-green' : 'badge-blue' }}">
                            {{ ucfirst($order->payment_method) }}
                        </span>
                    </td>
                    <td class="text-right font-semibold text-gray-900">${{ number_format($order->total_amount, 2) }}</td>
                    <td class="text-gray-400 text-xs">{{ $order->created_at->format('M d, Y  H:i') }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.orders.show', $order) }}"
                           class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-10 text-gray-400">No orders yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
