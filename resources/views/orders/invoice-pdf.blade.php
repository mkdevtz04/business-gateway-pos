<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }
        h1, h2, h3 { margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .flex { display: flex; justify-content: space-between; }
        .mt-4 { margin-top: 16px; }
        .summary { background: #f5f5f5; padding: 10px; border-radius: 6px; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="flex">
        <div>
            <h1>Invoice</h1>
            <p>Order #{{ $order->id }}</p>
            <p>Date: {{ $order->created_at->format('F j, Y, g:i a') }}</p>
        </div>
        <div style="text-align:right;">
            <h3>Processed By:</h3>
            <p>{{ $order->user->name }}</p>
            <p>{{ $order->user->email }}</p>
        </div>
    </div>

    <!-- Customer Info -->
    <div class="flex mt-4">
        <div>
            <h3>Customer:</h3>
            @if($order->customer)
                <p>{{ $order->customer->name }}</p>
                @if($order->customer->contact)
                    <p>{{ $order->customer->contact }}</p>
                @endif
            @else
                <p>Walk-in Customer</p>
            @endif
        </div>
        <div style="text-align:right;">
            <h3>Payment Method:</h3>
            <p>{{ ucfirst($order->payment_method) }}</p>
        </div>
    </div>

    <!-- Items Table -->
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th style="text-align:center;">Quantity</th>
                <th style="text-align:right;">Unit Price</th>
                <th style="text-align:right;">Tax</th>
                <th style="text-align:right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td style="text-align:center;">{{ $item->quantity }}</td>
                    <td style="text-align:right;">${{ number_format($item->price, 2) }}</td>
                    <td style="text-align:right;">${{ number_format($item->tax, 2) }}</td>
                    <td style="text-align:right;">${{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary -->
    <div class="flex mt-4">
        <div></div>
        <div class="summary" style="width: 250px;">
            <div class="flex">
                <span>Subtotal:</span>
                <span>${{ number_format($order->items->sum('subtotal'), 2) }}</span>
            </div>
            <div class="flex">
                <span>Tax:</span>
                <span>${{ number_format($order->items->sum('tax'), 2) }}</span>
            </div>
            <div class="flex" style="font-weight:bold; font-size:16px; margin-top:8px;">
                <span>Total:</span>
                <span>${{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>
</body>
</html>
