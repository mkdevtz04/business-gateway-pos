@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Customers</h1>
        <p class="page-subtitle">All registered customers and their spend history</p>
    </div>
</div>

{{-- KPI row --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-5 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
            <i class="fas fa-users text-blue-500"></i>
        </div>
        <p class="text-sm text-gray-500 font-medium">Total Customers</p>
        <p class="text-2xl font-extrabold text-gray-900 mt-0.5">{{ number_format($totalCustomers) }}</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mb-3">
            <i class="fas fa-dollar-sign text-emerald-500"></i>
        </div>
        <p class="text-sm text-gray-500 font-medium">Total Revenue</p>
        <p class="text-2xl font-extrabold text-gray-900 mt-0.5">${{ number_format($totalRevenue, 2) }}</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center mb-3">
            <i class="fas fa-chart-line text-violet-500"></i>
        </div>
        <p class="text-sm text-gray-500 font-medium">Avg. Spend / Customer</p>
        <p class="text-2xl font-extrabold text-gray-900 mt-0.5">
            ${{ $totalCustomers > 0 ? number_format($totalRevenue / $totalCustomers, 2) : '0.00' }}
        </p>
    </div>
</div>

{{-- Table card --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
    {{-- Toolbar --}}
    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
        <form method="GET" class="flex-1 max-w-sm">
            <div class="search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input name="search" type="text" value="{{ request('search') }}"
                       class="form-input bg-gray-50"
                       placeholder="Search by name or contact…">
            </div>
        </form>
        @if(request('search'))
            <a href="{{ route('customers.index') }}" class="text-sm text-gray-400 hover:text-gray-600">Clear</a>
        @endif
        <span class="text-sm text-gray-400 ml-auto">{{ $customers->count() }} customers</span>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table" id="customers-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>Orders</th>
                    <th>Total Spent</th>
                    <th>Joined</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    {{-- Avatar + name --}}
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($customer->name, 0, 2)) }}
                            </div>
                            <a href="{{ route('customers.show', $customer) }}"
                               class="font-semibold text-gray-900 hover:text-blue-600 transition-colors">
                                {{ $customer->name }}
                            </a>
                        </div>
                    </td>
                    <td class="text-gray-500">{{ $customer->contact ?: '—' }}</td>
                    <td>
                        <span class="badge badge-blue">{{ $customer->orders_count }}</span>
                    </td>
                    <td class="font-semibold text-gray-900">
                        ${{ number_format($customer->orders_sum_total_amount ?? 0, 2) }}
                    </td>
                    <td class="text-gray-400 text-xs">{{ $customer->created_at->format('M d, Y') }}</td>
                    <td class="text-center" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false"
                                class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center mx-auto transition-colors text-gray-500">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-4 z-10 bg-white rounded-xl shadow-lg border border-gray-100 py-1 min-w-[130px] text-left">
                            <a href="{{ route('customers.show', $customer) }}"
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-eye text-xs text-gray-400 w-4"></i> View
                            </a>
                            <a href="{{ route('customers.edit', $customer) }}"
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-pencil-alt text-xs text-gray-400 w-4"></i> Edit
                            </a>
                            <form action="{{ route('customers.destroy', $customer) }}" method="POST"
                                  data-confirm="Remove {{ $customer->name }}? Their orders will be kept.">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="fas fa-trash text-xs w-4"></i> Remove
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-14 text-gray-400">
                        <i class="fas fa-users text-3xl mb-3 block text-gray-200"></i>
                        No customers yet. They are created automatically when a sale is made.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
