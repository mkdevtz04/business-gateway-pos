@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-md print:shadow-none print:border-0">
            <!-- Invoice Header -->
            <div class="flex justify-between items-start mb-8 border-b pb-4">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Invoice</h1>
                    <p class="text-gray-500">Order #{{ $order->id }}</p>
                    <p class="text-gray-500">Date: {{ $order->created_at->format('F j, Y, g:i a') }}</p>
                </div>
                <div class="text-right">
                    <h2 class="text-lg font-semibold">Processed By:</h2>
                    <p>{{ $order->user->name }}</p>
                    <p class="text-gray-500">{{ $order->user->email }}</p>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div>
                    <h2 class="text-lg font-semibold">Customer:</h2>
                    @if ($order->customer)
                        <p>{{ $order->customer->name }}</p>
                        @if ($order->customer->contact)
                            <p class="text-gray-500">{{ $order->customer->contact }}</p>
                        @endif
                    @else
                        <p>Walk-in Customer</p>
                    @endif
                </div>
                <div>
                    <h2 class="text-lg font-semibold">Payment Method:</h2>
                    <p>{{ ucfirst($order->payment_method) }}</p>
                </div>
            </div>

            <!-- Order Items Table -->
            <div class="overflow-x-auto mb-8">
                <table class="min-w-full border border-gray-200">
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
                        @foreach ($order->items as $item)
                            <tr class="border-b">
                                <td class="py-3 px-4 font-medium">{{ $item->product->name }}</td>
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
            <div class="flex justify-end mb-6">
                <div class="w-full md:w-1/3 bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between mb-2">
                        <span class="font-semibold text-gray-600">Subtotal:</span>
                        <span>${{ number_format($order->items->sum('subtotal'), 2) }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="font-semibold text-gray-600">Tax:</span>
                        <span>${{ number_format($order->items->sum('tax'), 2) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-xl border-t pt-2">
                        <span>Total:</span>
                        <span>${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="text-center print:hidden"> {{-- <--- Add print:hidden here --}}
                {{-- <button  class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition mr-4">
                <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Invoice
            </button> --}}

                <a href="{{ route('orders.pdf', $order->id) }}"
                    class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700">
                    Download PDF
                </a>


                <a href="{{ route('orders.index') }}" class="text-gray-600 hover:underline">
                    &larr; Back to Order History
                </a>
            </div>
        </div>
    </div>
    </div>
    </div>
@endsection
