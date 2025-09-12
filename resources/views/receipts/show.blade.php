<!DOCTYPE html>
<html>
<head>
    <title>Receipt #{{ $order->id }}</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; font-size: 13pt; background: #f8fafc; color: #222; }
        .receipt-container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.07); padding: 32px; }
        .logo { width: 120px; margin-bottom: 16px; }
        .header { text-align: center; margin-bottom: 24px; }
        .header h1 { font-size: 1.7rem; font-weight: 700; margin: 0; color: #2545d4; }
        .details { margin-bottom: 24px; }
        .details p { margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th, td { border: 1px solid #e5e7eb; padding: 10px 8px; text-align: left; }
        th { background: #f3f4f6; font-weight: 600; }
        .summary { text-align: right; margin-top: 16px; }
        .summary p { margin: 2px 0; }
        .footer { text-align: center; color: #888; font-size: 11pt; margin-top: 32px; }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <img src="{{ asset('storage/logos/mkdev.png') }}" alt="Business Logo" class="logo">
            <h1>Business Gateway POS</h1>
            <p>Official Receipt</p>
        </div>
        <div class="details">
            <p><strong>Order ID:</strong> #{{ $order->id }}</p>
            <p><strong>Date:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
            <p><strong>Customer:</strong> {{ $order->customer->name ?? 'Walk-in' }}</p>
            <p><strong>Contact:</strong> {{ $order->customer->contact ?? 'N/A' }}</p>
            <p><strong>Clerk:</strong> {{ $order->user->name }}</p>
            <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) ?? 'N/A' }}</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Discount</th>
                    <th>Tax</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->discount ?? 0 }}%</td>
                    <td>${{ number_format($item->tax, 2) }}</td>
                    <td>${{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="summary">
            <p><strong>Subtotal:</strong> ${{ number_format($order->total_amount - $order->total_tax + $order->total_discount, 2) }}</p>
            <p><strong>Total Discount:</strong> ${{ number_format($order->total_discount, 2) }}</p>
            <p><strong>Total Tax:</strong> ${{ number_format($order->total_tax, 2) }}</p>
            <p style="font-size: 1.2em; font-weight: bold; color: #2545d4;">
                Total Due: ${{ number_format($order->total_amount, 2) }}
            </p>
        </div>
        <div class="footer">
            Thank you for your business!<br>
            Powered by Business Gateway POS
        </div>
    </div>
</body>
</html>