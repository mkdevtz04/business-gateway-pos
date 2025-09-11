// resources/views/receipts/show.blade.php
<!DOCTYPE html>
<html>
<head>
    <title>Receipt #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12pt; }
        .logo { width: 100px; margin-bottom: 20px; }
        .details { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <img src="{{ public_path('storage/logos/mkdev.png') }}" class="logo">
    <h1>Business Gateway POS</h1>
    <div class="details">
        <p><strong>Order ID:</strong> {{ $order->id }}</p>
        <p><strong>Customer:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
        <p><strong>Contact:</strong> {{ $order->customer->contact ?? 'N/A' }}</p>
        <p><strong>Clerk:</strong> {{ $order->user->name }}</p>
        <p><strong>Date:</strong> {{ $order->created_at->format('Y-m-d') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Discount</th>
                <th>Tax</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $order->product->name }}</td>
                <td>{{ $order->quantity }}</td>
                <td>${{ number_format($order->product->price, 2) }}</td>
                <td>{{ $order->discount }}%</td>
                <td>${{ number_format($order->tax, 2) }}</td>
                <td>${{ number_format($order->amount_due, 2) }}</td>
            </tr>
        </tbody>
    </table>
    <p><strong>Payment Method:</strong> {{ $order->payment_method ?? 'N/A' }}</p>
    <p><strong>Total Amount Due:</strong> ${{ number_format($order->amount_due, 2) }}</p>
</body>
</html>