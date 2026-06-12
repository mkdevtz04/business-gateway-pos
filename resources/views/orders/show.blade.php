@extends('layouts.app')

@section('content')
<div class="max-w-3xl">
    <div class="page-header print:hidden">
        <div>
            <h1 class="page-title">Invoice #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h1>
            <p class="page-subtitle">{{ $order->created_at->format('l, F d, Y \a\t g:i A') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('orders.pdf', $order->id) }}"
               class="btn btn-primary">
                <i class="fas fa-download text-xs"></i>
                Download PDF
            </a>
            @if(in_array(auth()->user()->role, ['admin','owner']))
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left text-xs"></i>
                    Back
                </a>
            @else
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left text-xs"></i>
                    Back
                </a>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden print:border-0 print:shadow-none">

        {{-- Invoice header --}}
        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-start">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-cash-register text-white text-sm"></i>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900">Business Gateway POS</div>
                        <div class="text-xs text-gray-400">Point of Sale System</div>
                    </div>
                </div>
                <span class="badge badge-green">{{ ucfirst($order->status) }}</span>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</p>
                <p class="text-sm text-gray-400 mt-1">{{ $order->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        {{-- Meta grid --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-0 border-b border-gray-100">
            <div class="px-8 py-5 border-r border-gray-100">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Customer</p>
                @if($order->customer)
                    <p class="font-semibold text-gray-900">{{ $order->customer->name }}</p>
                    @if($order->customer->contact)
                        <p class="text-sm text-gray-500 mt-0.5">{{ $order->customer->contact }}</p>
                    @endif
                @else
                    <p class="font-semibold text-gray-900">Walk-in Customer</p>
                @endif
            </div>
            <div class="px-8 py-5 border-r border-gray-100">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Processed By</p>
                <p class="font-semibold text-gray-900">{{ $order->user?->name ?? '—' }}</p>
                <p class="text-sm text-gray-500 mt-0.5">{{ $order->user?->email ?? '' }}</p>
            </div>
            <div class="px-8 py-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Payment</p>
                @if($order->payment_method === 'cash')
                    <span class="badge badge-green"><i class="fas fa-money-bill-wave mr-1.5 text-xs"></i>Cash</span>
                @else
                    <span class="badge badge-blue"><i class="fas fa-credit-card mr-1.5 text-xs"></i>Credit Card</span>
                @endif
            </div>
        </div>

        {{-- Items --}}
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Tax</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td class="font-medium text-gray-900">{{ $item->product?->name ?? 'Deleted Product' }}</td>
                        <td class="text-center">
                            <span class="badge badge-gray">{{ $item->quantity }}</span>
                        </td>
                        <td class="text-right text-gray-600">${{ number_format($item->price, 2) }}</td>
                        <td class="text-right text-gray-500 text-sm">${{ number_format($item->tax, 2) }}</td>
                        <td class="text-right font-semibold text-gray-900">${{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Summary --}}
        <div class="px-8 py-5 flex justify-end border-t border-gray-100">
            <div class="w-full max-w-xs space-y-2">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span>${{ number_format($order->items->sum('subtotal'), 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Tax</span>
                    <span>${{ number_format($order->items->sum('tax'), 2) }}</span>
                </div>
                <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t border-gray-200">
                    <span>Total</span>
                    <span>${{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-400">Thank you for your business &mdash; Business Gateway POS</p>
        </div>
    </div>
</div>
@endsection
