@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Sales Dashboard</h1>
            <p>Welcome, {{ auth()->user()->name }}! Here's your sales overview.</p>
        </div>
    </div>

    <!-- Sales Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-header">Today's Sales</div>
                <div class="card-body">
                    <h5 class="card-title">${{ number_format($todaySales, 2) }}</h5>
                    <p class="card-text">{{ $todayOrdersCount }} orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-header">This Week's Sales</div>
                <div class="card-body">
                    <h5 class="card-title">${{ number_format($weekSales, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-header">This Month's Sales</div>
                <div class="card-body">
                    <h5 class="card-title">${{ number_format($monthSales, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-header">Total Sales</div>
                <div class="card-body">
                    <h5 class="card-title">${{ number_format($totalSales, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Sales Over Last 7 Days</div>
                <div class="card-body">
                    <canvas id="salesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products and Recent Orders -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Top Selling Products</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity Sold</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topProducts as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->total_quantity }}</td>
                                </tr>
                            @endforeach
                            @if ($topProducts->isEmpty())
                                <tr>
                                    <td colspan="2">No products sold yet.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Recent Orders</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentOrders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->customer ? $order->customer->name : 'Guest' }}</td>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                    <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                            @if ($recentOrders->isEmpty())
                                <tr>
                                    <td colspan="4">No recent orders.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesData = @json($last7Days);

        const labels = salesData.map(item => item.date);
        const data = salesData.map(item => item.sales);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Daily Sales',
                    data: data,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection