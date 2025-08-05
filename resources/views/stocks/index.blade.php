// resources/views/stocks/index.blade.php
@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Stock Management</h2>
    @if ($lowStockCount > 0)
        <div class="bg-yellow-100 p-4 rounded mb-4">
            <p class="text-yellow-800">Warning: {{ $lowStockCount }} products low on stock!</p>
        </div>
    @endif
    <form method="GET" action="{{ route('stocks.index') }}" class="mb-4 flex space-x-4">
        <input name="search" type="text" placeholder="Search by Name" class="p-3 border rounded w-1/3" value="{{ request('search') }}">
        <select name="category_id" class="p-3 border rounded">
            <option value="">All Categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filter</button>
    </form>
    <div class="mb-4">
        <a href="{{ route('stocks.export.pdf') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Export PDF</a>
        <a href="{{ route('stocks.export.csv') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Export CSV</a>
    </div>
    <table class="w-full border">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-3 text-left">Name</th>
                <th class="p-3 text-left">Category</th>
                <th class="p-3 text-left">Quantity</th>
                <th class="p-3 text-left">Size</th>
                <th class="p-3 text-left">Price</th>
                <th class="p-3 text-left">Tax Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr @if ($product->quantity_available < 10) class="bg-yellow-50" @endif>
                    <td class="p-3">{{ $product->name }}</td>
                    <td class="p-3">{{ $product->category ? $product->category->name : 'N/A' }}</td>
                    <td class="p-3">{{ $product->quantity_available }}</td>
                    <td class="p-3">{{ $product->size ?? 'N/A' }}</td>
                    <td class="p-3">${{ number_format($product->price, 2) }}</td>
                    <td class="p-3">{{ $product->tax_rate }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection