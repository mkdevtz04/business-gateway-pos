@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h1 class="text-2xl font-bold mb-6">Order History</h1>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="w-1/9 py-3 px-4 uppercase font-semibold text-sm text-left">Order ID</th>
                            <th class="w-1/6 py-3 px-4 uppercase font-semibold text-sm text-left">Clerk</th>
                            <th class="w-1/6 py-3 px-4 uppercase font-semibold text-sm text-left">Customer</th>
                            <th class="w-1/9 py-3 px-4 uppercase font-semibold text-sm text-left">Total Amount</th>
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Status</th>
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Date</th>
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse($orders as $order)
                            <tr class="border-b">
                                <td class="py-3 px-4">#{{ $order->id }}</td>
                                <td class="py-3 px-4">{{ $order->user->name ?? 'N/A' }}</td>
                                <!-- ... -->
                                <td class="py-3 px-4">{{ $order->customer->name ?? 'Walk-in' }}</td>
                                <!-- ... -->
                                <td class="py-3 px-4">${{ number_format($order->total_amount, 2) }}</td>
                                <td class="py-3 px-4">
                                    <span class="bg-green-200 text-green-700 py-1 px-3 rounded-full text-xs">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="py-3 px-4 text-center">
                                    <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-800">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection
