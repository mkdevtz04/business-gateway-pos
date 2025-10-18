@extends('layouts.app')

@section('content')
<div class="admin-dashboard p-4"> {{-- Added p-4 for padding around the dashboard content --}}
    <div class="max-w-full mx-auto"> {{-- Replaced container-fluid with Tailwind's max-width and auto margins --}}
        <!-- Header -->
        <div class="dashboard-header mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-4 md:mb-0"> {{-- Adjusted margin for responsiveness --}}
                <h1 class="page-title">
                    <i class="fas fa-tachometer-alt"></i>
                    Admin Dashboard
                </h1>
            </div>
            <div class="text-right">
                <div class="date-display">
                    <i class="fas fa-calendar-alt"></i>
                    {{ now()->format('l, F d, Y') }}
                </div>
            </div>
        </div>

        <!-- All Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6"> {{-- Tailwind grid classes for responsive 1, 2, or 3 columns --}}
            <!-- Card 1 -->
            <div> {{-- Removed col-lg-4 col-md-6 mb-4 --}}
                <div class="dashboard-card">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-label">Total Revenue</div>
                        <div class="card-value">${{ number_format($totalSales, 0) }}</div>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div> {{-- Removed col-lg-4 col-md-6 mb-4 --}}
                <div class="dashboard-card">
                    <div class="card-icon bg-success">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-label">Total Orders</div>
                        <div class="card-value">{{ number_format($totalOrders) }}</div>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div> {{-- Removed col-lg-4 col-md-6 mb-4 --}}
                <div class="dashboard-card">
                    <div class="card-icon bg-info">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-label">Active Clerks</div>
                        <div class="card-value">{{ $totalClerks }}</div>
                    </div>
                </div>
            </div>

            <!-- Card 4 -->
            <div> {{-- Removed col-lg-4 col-md-6 mb-4 --}}
                <div class="dashboard-card">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-label">Avg Order Value</div>
                        <div class="card-value">${{ number_format($averageOrderValue, 0) }}</div>
                    </div>
                </div>
            </div>

            <!-- Card 5 -->
            <div> {{-- Removed col-lg-4 col-md-6 mb-4 --}}
                <div class="dashboard-card">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-label">Today's Sales</div>
                        <div class="card-value">$0</div> {{-- Placeholder, ensure this variable is passed from controller --}}
                    </div>
                </div>
            </div>

            <!-- Card 6 -->
            <div> {{-- Removed col-lg-4 col-md-6 mb-4 --}}
                <div class="dashboard-card">
                    <div class="card-icon bg-secondary">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-label">Total Products</div>
                        <div class="card-value">0</div> {{-- Placeholder, ensure this variable is passed from controller --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Add your chart rows here, using Tailwind grid/flex --}}
        {{-- Example for a chart row: --}}
        {{-- <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div>
                <div class="chart-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-chart-line"></i>
                            Sales Trend
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesTrendChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div>
                <div class="chart-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-chart-pie"></i>
                            Sales Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesDistributionChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div> --}}

    </div>
</div>

<!-- Chart.js Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const colors = {
        primary: '#667eea',
        success: '#28a745',
        info: '#17a2b8',
        warning: '#ffc107',
        danger: '#dc3545',
        secondary: '#6c757d' // Added secondary color
    };

    // Ensure you have the data for these charts passed from your controller
    // For example: $salesTrend, $salesByCategory, $monthlySales, $topClerks, $hourlyData, $topProducts
    // If any of these are undefined, the chart will not render and might cause JS errors.

    // Example: Sales Trend Chart (if you have $salesTrend data)
    const salesTrendCtx = document.getElementById('salesTrendChart');
    if (salesTrendCtx) {
        const salesTrendData = @json($salesTrend ?? []); // Use null coalescing to prevent errors if variable is not set
        new Chart(salesTrendCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: salesTrendData.map(item => item.date),
                datasets: [{
                    label: 'Sales',
                    data: salesTrendData.map(item => item.sales),
                    borderColor: colors.primary,
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Example: Category Chart (if you have $salesByCategory data)
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        const categoryData = @json($salesByCategory ?? []);
        new Chart(categoryCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: categoryData.map(item => item.category_name),
                datasets: [{
                    data: categoryData.map(item => item.total_sales),
                    backgroundColor: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 10, font: { size: 10 } }
                    }
                }
            }
        });
    }

    // Monthly Sales Chart
    const monthlySalesCtx = document.getElementById('monthlySalesChart');
    if (monthlySalesCtx) {
        const monthlySalesData = @json($monthlySales ?? []);
        new Chart(monthlySalesCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: monthlySalesData.map(item => item.month),
                datasets: [{
                    label: 'Sales',
                    data: monthlySalesData.map(item => item.sales),
                    backgroundColor: colors.primary
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Clerk Performance Chart
    const clerkCtx = document.getElementById('clerkPerformanceChart');
    if (clerkCtx) {
        const clerkData = @json($topClerks ?? []);
        new Chart(clerkCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: clerkData.map(item => item.name),
                datasets: [{
                    label: 'Sales',
                    data: clerkData.map(item => item.total_sales),
                    backgroundColor: colors.success
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Hourly Chart
    const hourlyCtx = document.getElementById('hourlyChart');
    if (hourlyCtx) {
        const hourlyData = @json($hourlyData ?? []);
        new Chart(hourlyCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: hourlyData.map(item => item.hour),
                datasets: [{
                    label: 'Sales',
                    data: hourlyData.map(item => item.sales),
                    backgroundColor: colors.info
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Top Products Chart
    const productsCtx = document.getElementById('topProductsChart');
    if (productsCtx) {
        const productsData = @json($topProducts ?? []);
        new Chart(productsCtx.getContext('2d'), {
            type: 'pie',
            data: {
                labels: productsData.map(item => item.name),
                datasets: [{
                    data: productsData.map(item => item.total_quantity),
                    backgroundColor: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 10, font: { size: 10 } }
                    }
                }
            }
        });
    }
});
</script>

<style>
.admin-dashboard {
    background: #f5f7fa;
    min-height: 100vh;
    /* Removed padding: 20px 0; as p-4 is added to the div directly */
}

.dashboard-header {
    /* Removed padding: 20px 0; as mb-6 handles spacing */
    /* flexbox properties moved to HTML classes */
}

.page-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.page-title i {
    margin-right: 10px;
    color: #667eea;
}

.date-display {
    font-size: 0.9rem;
    color: #6c757d;
}

.date-display i {
    margin-right: 8px;
}

/* Dashboard Cards */
.dashboard-card {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    height: 200px;
    justify-content: center;
}

.card-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    margin-bottom: 20px;
}

.card-icon.bg-primary { background: #667eea; }
.card-icon.bg-success { background: #28a745; }
.card-icon.bg-info { background: #17a2b8; }
.card-icon.bg-warning { background: #ffc107; }
.card-icon.bg-danger { background: #dc3545; }
.card-icon.bg-secondary { background: #6c757d; }

.card-content {
    width: 100%;
}

.card-label {
    font-size: 0.95rem;
    color: #6c757d;
    margin-bottom: 10px;
    font-weight: 500;
}

.card-value {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
}

@media (max-width: 768px) {
    .page-title {
        font-size: 1.5rem;
    }
    
    .card-value {
        font-size: 1.5rem;
    }
    
    .dashboard-card {
        height: 180px;
    }
}
</style>
@endsection