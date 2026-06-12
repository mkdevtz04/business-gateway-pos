@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Categories</h1>
        <p class="page-subtitle">Organise your products by category</p>
    </div>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">
        <i class="fas fa-plus text-xs"></i>
        Add Category
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
        <div class="search-wrapper flex-1 max-w-xs">
            <i class="fas fa-search search-icon"></i>
            <input id="table-search" type="text" class="form-input" placeholder="Search categories…">
        </div>
        <span id="row-count" class="text-sm text-gray-400 ml-auto"></span>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table" id="categories-table">
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th>Products</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr data-search="{{ strtolower($category->name) }}">
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-tag text-blue-500 text-xs"></i>
                            </div>
                            <span class="font-semibold text-gray-900">{{ $category->name }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-gray">
                            {{ $category->products_count }} {{ Str::plural('product', $category->products_count) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('categories.edit', $category) }}"
                               class="btn btn-secondary btn-sm btn-icon" title="Edit">
                                <i class="fas fa-pencil-alt text-xs"></i>
                            </a>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                  data-confirm="Delete category '{{ $category->name }}'? This cannot be undone.">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center py-12 text-gray-400">
                        <i class="fas fa-tags text-3xl mb-3 block text-gray-200"></i>
                        No categories yet. <a href="{{ route('categories.create') }}" class="text-blue-600 hover:underline">Add your first category</a>.
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
    const search  = document.getElementById('table-search');
    const rows    = document.querySelectorAll('#categories-table tbody tr[data-search]');
    const countEl = document.getElementById('row-count');
    const total   = rows.length;
    function update() {
        const q = search.value.trim().toLowerCase();
        let visible = 0;
        rows.forEach(r => {
            const match = !q || r.dataset.search.includes(q);
            r.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        countEl.textContent = `${visible} / ${total} categories`;
    }
    search.addEventListener('input', update);
    update();
})();
</script>
@endpush
@endsection
