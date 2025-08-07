@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white p-8 rounded-lg shadow-md">
        <!-- Header -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold">Order #{{ $order->id }}</h1>
                <p class="text-gray-500">Date: {{ $order->created_at->format('F j, Y, g:i a') }}</p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-semibold">Billed To:</h2>
                <p>{{ $order->user->name }}</p>
                <p>{{ $order->user->email }}</p>
            </div>
        </div>

        <!-- Order Items Table -->
        <div class="overflow-x-auto mb-6">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 text-left">Product</th>
                        <th class="py-2 px-4 text-center">Quantity</th>
                        <th class="py-2 px-4 text-right">Unit Price</th>
                        <th class="py-2 px-4 text-right">Tax</th>
                        <th class="py-2 px-4 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr class="border-b">
                            <td class="py-3 px-4 font-semibold">{{ $item->product->name }}</td>
                            <td class="py-3 px-4 text-center">{{ $item->quantity }}</td>
                            <td class="py-3 px-4 text-right">${{ number_format($item->price, 2) }}</td>
                            <td class="py-3 px-4 text-right">${{ number_format($item->tax, 2) }}</td>
                            <td class="py-3 px-4 text-right">${{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Order Summary -->
        <div class="flex justify-end">
            <div class="w-full md:w-1/3">
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-600">Payment Method:</span>
                        <span>{{ ucfirst($order->payment_method) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-xl border-t pt-2">
                        <span>Total Amount:</span>
                        <span>${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="text-center mt-8">
            <a href="{{ route('orders.index') }}" class="text-blue-600 hover:underline">
                &larr; Back to Order History
            </a>
        </div>
    </div>
</div>
@endsection