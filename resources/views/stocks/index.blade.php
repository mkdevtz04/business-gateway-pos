@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Stock Management</h1>
        <p class="page-subtitle">Monitor inventory levels across all products</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('stocks.export.pdf') }}" class="btn btn-secondary">
            <i class="fas fa-file-pdf text-xs text-red-500"></i> PDF
        </a>
        <a href="{{ route('stocks.export.csv') }}" class="btn btn-dark">
            <i class="fas fa-download text-xs"></i> Export CSV
        </a>
    </div>
</div>

{{-- Low stock alert banner --}}
@if($lowStockCount > 0)
<div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 mb-6 flex items-center gap-3">
    <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
        <i class="fas fa-exclamation-triangle text-amber-500"></i>
    </div>
    <div>
        <p class="text-sm font-semibold text-amber-800">
            {{ $lowStockCount }} {{ Str::plural('product', $lowStockCount) }} running low on stock
        </p>
        <p class="text-xs text-amber-600 mt-0.5">Items with fewer than 10 units are highlighted below.</p>
    </div>
</div>
@endif

{{-- Table card --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
    {{-- Toolbar --}}
    <form method="GET" class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center gap-3">
        <div class="search-wrapper flex-1 min-w-0 max-w-sm">
            <i class="fas fa-search search-icon"></i>
            <input name="search" type="text" value="{{ request('search') }}"
                   class="form-input bg-gray-50" placeholder="Search products…">
        </div>
        <select name="category_id" class="form-input w-auto text-sm bg-gray-50 pr-8">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
        <select name="stock_status" class="form-input w-auto text-sm bg-gray-50 pr-8">
            <option value="">All Stock</option>
            <option value="out"  {{ request('stock_status') === 'out'  ? 'selected' : '' }}>Out of Stock</option>
            <option value="low"  {{ request('stock_status') === 'low'  ? 'selected' : '' }}>Low Stock</option>
            <option value="ok"   {{ request('stock_status') === 'ok'   ? 'selected' : '' }}>In Stock</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        @if(request()->hasAny(['search','category_id','stock_status']))
            <a href="{{ route('stocks.index') }}" class="text-sm text-gray-400 hover:text-gray-600">Reset</a>
        @endif
        <span class="text-sm text-gray-400 ml-auto">{{ $products->count() }} products</span>
    </form>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th class="text-center">Qty</th>
                    <th>Size</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Tax</th>
                    <th>Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                @php
                    $qty    = $product->quantity_available;
                    $status = $qty <= 0 ? 'out' : ($qty < 10 ? 'low' : 'ok');

                    // Filter by stock_status if set
                    if (request('stock_status') && request('stock_status') !== $status) continue;
                @endphp
                <tr class="{{ $status === 'out' ? 'bg-red-50/50' : ($status === 'low' ? 'bg-amber-50/40' : '') }}">
                    <td>
                        <div class="flex items-center gap-3">
                            @if($product->image_path)
                                <div class="w-9 h-9 rounded-lg bg-white border border-gray-100 flex items-center justify-center p-1 flex-shrink-0">
                                    <img src="{{ asset('storage/' . $product->image_path) }}" class="w-full h-full object-contain">
                                </div>
                            @else
                                <div class="w-9 h-9 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-box text-gray-300 text-xs"></i>
                                </div>
                            @endif
                            <span class="font-semibold text-gray-900">{{ $product->name }}</span>
                        </div>
                    </td>
                    <td>
                        @if($product->category)
                            <span class="badge badge-blue">{{ $product->category->name }}</span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="font-bold text-lg {{ $status === 'out' ? 'text-red-600' : ($status === 'low' ? 'text-amber-600' : 'text-gray-900') }}">
                            {{ $qty }}
                        </span>
                    </td>
                    <td class="text-gray-500">{{ $product->size ?: '—' }}</td>
                    <td class="text-right font-semibold text-gray-900">${{ number_format($product->price, 2) }}</td>
                    <td class="text-right text-gray-500">{{ $product->tax_rate }}%</td>
                    <td>
                        @if($status === 'out')
                            <span class="status-outstock">Out of Stock</span>
                        @elseif($status === 'low')
                            <span class="status-lowstock">Low Stock</span>
                        @else
                            <span class="status-instock">In Stock</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('products.edit', $product) }}"
                           class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center mx-auto transition-colors text-gray-400 hover:text-blue-600">
                            <i class="fas fa-pencil-alt text-xs"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-14 text-gray-400">
                        <i class="fas fa-box-open text-3xl mb-3 block text-gray-200"></i>
                        No products found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
