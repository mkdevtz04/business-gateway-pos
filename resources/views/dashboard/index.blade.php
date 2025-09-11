@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Sales Dashboard</h1>
                    <p class="text-muted">Welcome back, {{ auth()->user()->name }}! Here's your sales overview.</p>
                </div>
                <div class="text-muted">
                    <i class="fas fa-calendar-alt"></i> {{ now()->format('F d, Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Today's Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($todaySales, 2) }}</div>
                            <div class="text-xs text-muted">{{ $todayOrdersCount }} {{ Str::plural('order', $todayOrdersCount) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">This Week's Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($weekSales, 2) }}</div>
                            @if(isset($weeklyGrowth))
                                <div class="text-xs {{ $weeklyGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                    <i class="fas fa-arrow-{{ $weeklyGrowth >= 0 ? 'up' : 'down' }}"></i> {{ abs($weeklyGrowth) }}%
                                </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">This Month's Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($monthSales, 2) }}</div>
                            @if(isset($monthlyGrowth))
                                <div class="text-xs {{ $monthlyGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                    <i class="fas fa-arrow-{{ $monthlyGrowth >= 0 ? 'up' : 'down' }}"></i> {{ abs($monthlyGrowth) }}%
                                </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Average Order Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($averageOrderValue, 2) }}</div>
                            <div class="text-xs text-muted">Total: ${{ number_format($totalSales, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Sales Trend Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Sales Trend - Last 7 Days</h6>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="100"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Sales by Category Pie Chart -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sales by Category</h6>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Charts Row -->
    <div class="row mb-4">
        <!-- Product Quantity Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Product Quantity Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="quantityChart" height="150"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Top Products Bar Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
                </div>
                <div class="card-body">
                    <canvas id="productsChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row">
        <!-- Top Products Table -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>Rank</th>
                                    <th>Product</th>
                                    <th>Quantity Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topProducts as $index => $product)
                                    <tr>
                                        <td>
                                            <span class="badge badge-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'light') }}">
                                                #{{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="font-weight-bold">{{ $product->name }}</td>
                                        <td>{{ number_format($product->total_quantity) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                            No products sold yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders Table -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <code>#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</code>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm rounded-circle bg-primary text-white mr-2">
                                                    {{ substr($order->customer ? $order->customer->name : 'Guest', 0, 1) }}
                                                </div>
                                                {{ $order->customer ? $order->customer->name : 'Guest' }}
                                            </div>
                                        </td>
                                        <td class="font-weight-bold text-success">${{ number_format($order->total_amount, 2) }}</td>
                                        <td class="text-muted">{{ $order->created_at->format('M d, H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-shopping-cart fa-2x mb-2"></i><br>
                                            No recent orders.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Color palette
    const colors = {
        primary: 'rgba(78, 115, 223, 1)',
        success: 'rgba(28, 200, 138, 1)',
        info: 'rgba(54, 185, 204, 1)',
        warning: 'rgba(246, 194, 62, 1)',
        danger: 'rgba(231, 74, 59, 1)',
        tea: 'rgba(255, 99, 132, 1)',
        coffee: 'rgba(54, 162, 235, 1)',
        soda: 'rgba(255, 205, 86, 1)',
        juice: 'rgba(75, 192, 192, 1)'
    };

    // Sales Trend Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesData = @json($last7Days);
    
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: salesData.map(item => item.date),
            datasets: [{
                label: 'Daily Sales ($)',
                data: salesData.map(item => item.sales),
                borderColor: colors.primary,
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: colors.primary,
                pointBorderColor: colors.primary,
                pointHoverBackgroundColor: colors.primary,
                pointHoverBorderColor: colors.primary,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            elements: {
                point: {
                    hoverRadius: 8
                }
            }
        }
    });

    // Sales by Category Pie Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryData = @json($salesByCategory);
    
    new Chart(categoryCtx, {
        type: 'pie',
        data: {
            labels: categoryData.map(item => item.category_name),
            datasets: [{
                data: categoryData.map(item => item.total_sales),
                backgroundColor: [
                    colors.tea,
                    colors.coffee,
                    colors.soda,
                    colors.juice,
                    colors.primary,
                    colors.success,
                    colors.warning
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Product Quantity Distribution Donut Chart
    const quantityCtx = document.getElementById('quantityChart').getContext('2d');
    const quantityData = @json($productQuantityDistribution);
    
    new Chart(quantityCtx, {
        type: 'doughnut',
        data: {
            labels: quantityData.map(item => item.category_name),
            datasets: [{
                data: quantityData.map(item => item.total_quantity),
                backgroundColor: [
                    colors.tea,
                    colors.coffee,
                    colors.soda,
                    colors.juice,
                    colors.primary,
                    colors.success,
                    colors.warning
                ],
                borderWidth: 3,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Top Products Bar Chart
    const productsCtx = document.getElementById('productsChart').getContext('2d');
    const topProductsData = @json($topProducts);
    
    new Chart(productsCtx, {
        type: 'bar',
        data: {
            labels: topProductsData.map(item => item.name),
            datasets: [{
                label: 'Quantity Sold',
                data: topProductsData.map(item => item.total_quantity),
                backgroundColor: colors.success,
                borderColor: colors.success,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.avatar {
    width: 2rem;
    height: 2rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 500;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
.text-gray-300 {
    color: #dddfeb !important;
}
.card {
    transition: all 0.3s;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection