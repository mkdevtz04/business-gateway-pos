<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 12px; 
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 15px;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .invoice-header {
            background: #2563eb;
            color: white;
            padding: 15px;
            border-radius: 5px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .company-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 5px;
            padding: 5px;
        }
        
        .company-details h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }
        
        .company-details p {
            margin: 2px 0 0 0;
            font-size: 12px;
        }
        
        .invoice-meta {
            text-align: right;
        }
        
        .invoice-meta h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
        }
        
        .invoice-meta p {
            margin: 2px 0;
        }
        
        .invoice-body {
            padding: 15px 0;
        }
        
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            gap: 15px;
        }
        
        .info-card {
            flex: 1;
            background: #f8fafc;
            padding: 10px;
            border-radius: 5px;
            border-left: 3px solid #2563eb;
        }
        
        .info-card h3 {
            margin: 0 0 5px 0;
            color: #2563eb;
            font-size: 14px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 11px;
        }
        
        .items-table th {
            background: #64748b;
            color: white;
            padding: 8px;
            text-align: left;
        }
        
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .quantity-badge {
            background: #dbeafe;
            color: #1d4ed8;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
        }
        
        .summary-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
        }
        
        .summary-card {
            background: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            min-width: 200px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .total-row {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 2px solid #2563eb;
            font-weight: bold;
            font-size: 13px;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 11px;
            color: #64748b;
        }

        .status-badge {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="header-content">
                <div class="company-info">
                    <img src="{{ public_path('storage/logos/mkdev.png') }}" alt="Logo" class="logo">
                    <div class="company-details">
                        <h1>Business Gateway POS</h1>
                        <p>Professional Point of Sale System</p>
                    </div>
                </div>
                <div class="invoice-meta">
                    <h2>INVOICE</h2>
                    <p><strong>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
                    <p>{{ $order->created_at->format('M j, Y') }}</p>
                    <div class="status-badge">Paid</div>
                </div>
            </div>
        </div>

        <div class="invoice-body">
            <div class="info-section">
                <div class="info-card">
                    <h3>Bill To</h3>
                    <p><strong>{{ $order->customer ? $order->customer->name : 'Walk-in Customer' }}</strong></p>
                    @if($order->customer && $order->customer->contact)
                        <p>{{ $order->customer->contact }}</p>
                    @endif
                </div>
                <div class="info-card">
                    <h3>Payment Info</h3>
                    <p><strong>Method:</strong> {{ ucfirst($order->payment_method) }}</p>
                    <p><strong>By:</strong> {{ $order->user->name }}</p>
                </div>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width:40%">Product</th>
                        <th style="width:15%" class="text-center">Qty</th>
                        <th style="width:15%" class="text-right">Price</th>
                        <th style="width:15%" class="text-right">Tax</th>
                        <th style="width:15%" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td class="text-center">
                            <span class="quantity-badge">{{ $item->quantity }}</span>
                        </td>
                        <td class="text-right">${{ number_format($item->price, 2) }}</td>
                        <td class="text-right">${{ number_format($item->tax, 2) }}</td>
                        <td class="text-right">${{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary-section">
                <div class="summary-card">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>${{ number_format($order->items->sum('subtotal') - $order->items->sum('tax'), 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax:</span>
                        <span>${{ number_format($order->items->sum('tax'), 2) }}</span>
                    </div>
                    @if($order->total_discount > 0)
                    <div class="summary-row">
                        <span>Discount:</span>
                        <span>-${{ number_format($order->total_discount, 2) }}</span>
                    </div>
                    @endif
                    <div class="summary-row total-row">
                        <span>Total:</span>
                        <span>${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="footer">
                <p><strong>Thank you for your business!</strong></p>
                <p>For questions about this invoice, please contact support@businessgateway.com</p>
            </div>
        </div>
    </div>
</body>
</html>