// resources/views/stocks/pdf.blade.php
<!DOCTYPE html>
<html>
<head>
    <title>Stock Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .low-stock { background-color: #fff7ed; }
    </style>
</head>
<body>
    <h1>Business Gateway POS - Stock Report</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Size</th>
                <th>Price</th>
                <th>Tax Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr @if ($product->quantity_available < 10) class="low-stock" @endif>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category ? $product->category->name : 'N/A' }}</td>
                    <td>{{ $product->quantity_available }}</td>
                    <td>{{ $product->size ?? 'N/A' }}</td>
                    <td>${{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->tax_rate }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>