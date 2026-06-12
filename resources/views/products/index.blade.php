@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Products</h1>
        <p class="page-subtitle">Manage your product inventory</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-dark">
        <i class="fas fa-plus text-xs"></i>
        Add Product
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">

    {{-- Toolbar --}}
    <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center gap-3">
        <div class="search-wrapper flex-1 min-w-0 max-w-sm">
            <i class="fas fa-search search-icon"></i>
            <input id="table-search" type="text" class="form-input bg-gray-50" placeholder="Search products…">
        </div>
        {{-- Filter by stock --}}
        <select id="stock-filter" class="form-input w-auto text-sm bg-gray-50 pr-8">
            <option value="">All</option>
            <option value="instock">In Stock</option>
            <option value="low">Low Stock</option>
            <option value="out">Out of Stock</option>
        </select>
        <span id="row-count" class="text-sm text-gray-400 ml-auto hidden sm:block"></span>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table" id="products-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Size</th>
                    <th>Tax</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                @php
                    $stockStatus = $product->quantity_available <= 0 ? 'out'
                                 : ($product->quantity_available < 10 ? 'low' : 'instock');
                @endphp
                <tr data-search="{{ strtolower($product->name . ' ' . $product->category?->name . ' ' . $product->size) }}"
                    data-stock="{{ $stockStatus }}">
                    {{-- Product --}}
                    <td>
                        <div class="flex items-center gap-3">
                            @if($product->image_path)
                                <div class="w-10 h-10 rounded-lg bg-white border border-gray-100 flex items-center justify-center flex-shrink-0 p-1">
                                    <img src="{{ asset('storage/' . $product->image_path) }}"
                                         class="w-full h-full object-contain">
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-box text-gray-300 text-sm"></i>
                                </div>
                            @endif
                            <span class="font-semibold text-gray-900">{{ $product->name }}</span>
                        </div>
                    </td>
                    {{-- Category --}}
                    <td>
                        @if($product->category)
                            <span class="badge badge-blue">{{ $product->category->name }}</span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    {{-- Price --}}
                    <td class="font-semibold text-gray-900">${{ number_format($product->price, 2) }}</td>
                    {{-- Stock qty --}}
                    <td class="font-medium text-gray-700">{{ $product->quantity_available }}</td>
                    {{-- Size --}}
                    <td class="text-gray-500">{{ $product->size ?: '—' }}</td>
                    {{-- Tax --}}
                    <td class="text-gray-500">{{ $product->tax_rate }}%</td>
                    {{-- Status badge --}}
                    <td>
                        @if($stockStatus === 'out')
                            <span class="status-outstock">Out of Stock</span>
                        @elseif($stockStatus === 'low')
                            <span class="status-lowstock">Low Stock</span>
                        @else
                            <span class="status-instock">In Stock</span>
                        @endif
                    </td>
                    {{-- 3-dot Actions --}}
                    <td class="text-center relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false"
                                class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center mx-auto transition-colors text-gray-500 hover:text-gray-700">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-4 top-10 z-10 bg-white rounded-xl shadow-lg border border-gray-100 py-1 min-w-[130px] text-left">
                            <a href="{{ route('products.edit', $product) }}"
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-pencil-alt text-xs text-gray-400 w-4"></i>
                                Edit
                            </a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST"
                                  data-confirm="Delete '{{ $product->name }}'? This cannot be undone.">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <i class="fas fa-trash text-xs w-4"></i>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-14 text-gray-400">
                        <i class="fas fa-box text-3xl mb-3 block text-gray-200"></i>
                        No products yet.
                        <a href="{{ route('products.create') }}" class="text-blue-600 hover:underline">Add your first product</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const search      = document.getElementById('table-search');
    const stockFilter = document.getElementById('stock-filter');
    const rows        = document.querySelectorAll('#products-table tbody tr[data-search]');
    const countEl     = document.getElementById('row-count');
    const total       = rows.length;

    function update() {
        const q     = search.value.trim().toLowerCase();
        const stock = stockFilter.value;
        let visible = 0;
        rows.forEach(r => {
            const matchQ = !q     || r.dataset.search.includes(q);
            const matchS = !stock || r.dataset.stock === stock;
            const show   = matchQ && matchS;
            r.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        countEl.textContent = `${visible} / ${total} products`;
    }

    search.addEventListener('input', update);
    stockFilter.addEventListener('change', update);
    update();
})();
</script>
@endpush
@endsection
