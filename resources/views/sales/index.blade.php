// resources/views/sales/index.blade.php
@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Sales</h2>
    <form method="GET" action="{{ route('sales.index') }}" class="mb-4">
        <input name="date" type="date" class="p-3 border rounded" value="{{ request('date') }}">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filter</button>
    </form>
    <table class="w-full border">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-3 text-left">Date</th>
                <th class="p-3 text-left">Product</th>
                <th class="p-3 text-left">Quantity</th>
                <th class="p-3 text-left">Discount</th>
                <th class="p-3 text-left">Total</th>
                <th class="p-3 text-left">Clerk</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
                <tr>
                    <td class="p-3">{{ $sale->sale_date }}</td>
                    <td class="p-3">{{ $sale->product->name }}</td>
                    <td class="p-3">{{ $sale->quantity }}</td>
                    <td class="p-3">{{ $sale->discount }}%</td>
                    <td class="p-3">${{ number_format($sale->total_amount, 2) }}</td>
                    <td class="p-3">{{ $sale->user->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection