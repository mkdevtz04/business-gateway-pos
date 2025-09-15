@extends('layouts.app')

@section('content')
<div class="clerk-dashboard">
    <!-- Header Section -->
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="dashboard-title">
                        <i class="fas fa-user-tie"></i>
                        My Sales Dashboard
                    </h1>
                    <p class="dashboard-subtitle">Welcome back, {{ auth()->user()->name }}! Track your personal sales performance.</p>
                </div>
                <div class="col-md-4 text-right">
                    <div class="date-info">
                        <i class="fas fa-calendar-alt"></i>
                        {{ now()->format('l, F d, Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Personal KPI Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">${{ number_format($todaySales, 0) }}</div>
                        <div class="stat-label">Today's Sales</div>
                        <div class="stat-extra">{{ $todayOrders }} {{ Str::plural('order', $todayOrders) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">${{ number_format($totalSales, 0) }}</div>
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-extra">All time earnings</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ number_format($totalOrders) }}</div>
                        <div class="stat-label">Total Orders</div>
                        <div class="stat-extra">Orders processed</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">${{ number_format($averageOrderValue, 0) }}</div>
                        <div class="stat-label">Avg Order Value</div>
                        <div class="stat-extra">Per transaction</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Charts Row -->
        <div class="row mb-4">
            <!-- Personal Sales Trend -->
            <div class="col-lg-8 mb-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fas fa-chart-area"></i>
                            My Sales Trend - Last 14 Days
                        </h3>
                    </div>
                    <div class="chart-body">
                        <canvas id="personalSalesTrend" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- My Category Performance -->
            <div class="col-lg-4 mb-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fas fa-chart-pie"></i>
                            My Sales by Category
                        </h3>
                    </div>
                    <div class="chart-body">
                        <canvas id="myCategoryChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Charts Row -->
        <div class="row mb-4">
            <!-- Weekly Performance -->
            <div class="col-lg-6 mb-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fas fa-chart-bar"></i>
                            Weekly Performance - Last 8 Weeks
                        </h3>
                    </div>
                    <div class="chart-body">
                        <canvas id="weeklyPerformanceChart" height="150"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Products I Sell -->
            <div class="col-lg-6 mb-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fas fa-star"></i>
                            My Top Selling Products
                        </h3>
                    </div>
                    <div class="chart-body">
                        <canvas id="myTopProductsChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Analytics Row -->
        <div class="row mb-4">
            <!-- Hourly Performance Pattern -->
            <div class="col-lg-12 mb-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fas fa-clock"></i>
                            My Performance Pattern - Average by Hour (Last 7 Days)
                        </h3>
                    </div>
                    <div class="chart-body">
                        <canvas id="hourlyPatternChart" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="performance-summary">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="performance-item">
                                <div class="performance-number">{{ number_format($totalOrders) }}</div>
                                <div class="performance-label">Orders Completed</div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="performance-item">
                                <div class="performance-number">${{ number_format($averageOrderValue, 2) }}</div>
                                <div class="performance-label">Average Sale</div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="performance-item">
                                <div class="performance-number">${{ number_format($todaySales, 2) }}</div>
                                <div class="performance-label">Today's Target</div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="performance-item">
                                <div class="performance-number">{{ count($topProducts) }}</div>
                                <div class="performance-label">Products Sold</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Personal color palette for clerk dashboard
    const colors = {
        primary: '#4F46E5',    // Indigo
        primaryLight: 'rgba(79, 70, 229, 0.1)',
        secondary: '#7C3AED',  // Purple
        secondaryLight: 'rgba(124, 58, 237, 0.1)',
        success: '#059669',    // Emerald
        successLight: 'rgba(5, 150, 105, 0.1)',
        info: '#0EA5E9',       // Sky Blue
        infoLight: 'rgba(14, 165, 233, 0.1)',
        warning: '#F59E0B',    // Amber
        warningLight: 'rgba(245, 158, 11, 0.1)',
        gradient: [
            '#4F46E5', // Indigo
            '#7C3AED', // Purple
            '#059669', // Emerald
            '#0EA5E9', // Sky Blue
            '#F59E0B', // Amber
            '#EC4899', // Pink
            '#8B5CF6', // Violet
            '#3B82F6'  // Blue
        ]
    };

    // Personal Sales Trend Chart
    const personalSalesCtx = document.getElementById('personalSalesTrend').getContext('2d');
    const personalSalesData = @json($salesTrend);
    
    new Chart(personalSalesCtx, {
        type: 'line',
        data: {
            labels: personalSalesData.map(item => item.date),
            datasets: [{
                label: 'My Sales ($)',
                data: personalSalesData.map(item => item.sales),
                borderColor: colors.primary,
                backgroundColor: colors.primaryLight,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: colors.primary,
                pointRadius: 5,
                pointHoverRadius: 8
            }, {
                label: 'Orders',
                data: personalSalesData.map(item => item.orders),
                borderColor: colors.success,
                backgroundColor: 'transparent',
                borderWidth: 2,
                borderDash: [5, 5],
                fill: false,
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    }
                }
            }
        }
    });

    // My Category Chart
    const myCategoryCtx = document.getElementById('myCategoryChart').getContext('2d');
    const myCategoryData = @json($salesByCategory);
    
    new Chart(myCategoryCtx, {
        type: 'doughnut',
        data: {
            labels: myCategoryData.map(item => item.category_name),
            datasets: [{
                data: myCategoryData.map(item => item.total_sales),
                backgroundColor: colors.gradient.slice(0, myCategoryData.length),
                borderWidth: 0,
                hoverBorderWidth: 3,
                hoverBorderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });

    // Weekly Performance Chart
    const weeklyPerformanceCtx = document.getElementById('weeklyPerformanceChart').getContext('2d');
    const weeklyPerformanceData = @json($weeklySales);
    
    new Chart(weeklyPerformanceCtx, {
        type: 'bar',
        data: {
            labels: weeklyPerformanceData.map(item => item.week),
            datasets: [{
                label: 'Weekly Sales',
                data: weeklyPerformanceData.map(item => item.sales),
                backgroundColor: colors.info,
                borderRadius: 8,
                borderSkipped: false
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
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // My Top Products Chart
    const myTopProductsCtx = document.getElementById('myTopProductsChart').getContext('2d');
    const myTopProductsData = @json($topProducts);
    
    new Chart(myTopProductsCtx, {
        type: 'horizontalBar',
        data: {
            labels: myTopProductsData.map(item => item.name.length > 15 ? item.name.substring(0, 15) + '...' : item.name),
            datasets: [{
                label: 'Quantity Sold',
                data: myTopProductsData.map(item => item.total_quantity),
                backgroundColor: colors.secondary,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });

    // Hourly Pattern Chart
    const hourlyPatternCtx = document.getElementById('hourlyPatternChart').getContext('2d');
    const hourlyPatternData = @json($hourlyData);
    
    new Chart(hourlyPatternCtx, {
        type: 'line',
        data: {
            labels: hourlyPatternData.map(item => item.hour),
            datasets: [{
                label: 'Average Sales per Hour',
                data: hourlyPatternData.map(item => item.avg_sales),
                borderColor: colors.warning,
                backgroundColor: colors.warningLight,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: colors.warning,
                pointRadius: 4
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
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toFixed(0);
                        }
                    }
                }
            }
        }
    });
});
</script>

<style>
.clerk-dashboard {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 0;
}

.dashboard-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.dashboard-title {
    font-size: 2.5rem;
    font-weight: 300;
    margin: 0;
}

.dashboard-title i {
    margin-right: 1rem;
}

.dashboard-subtitle {
    opacity: 0.9;
    margin: 0.5rem 0 0 0;
    font-size: 1.1rem;
}

.date-info {
    font-size: 1rem;
    opacity: 0.9;
}

.stat-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: none;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    opacity: 0.08;
    transform: translate(30%, -30%);
}

.stat-card.primary::before {
    background: #2196F3;
}

.stat-card.success::before {
    background: #4CAF50;
}

.stat-card.info::before {
    background: #00BCD4;
}

.stat-card.warning::before {
    background: #FF9800;
}

.stat-card:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 25px 50px rgba(0,0,0,0.2);
}

.stat-icon {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    font-size: 2.2rem;
    opacity: 0.2;
    z-index: 1;
}

.stat-content {
    position: relative;
    z-index: 2;
}

.stat-value {
    font-size: 2.8rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    line-height: 1;
}

.stat-label {
    font-size: 1rem;
    color: #7f8c8d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.25rem;
}

.stat-extra {
    font-size: 0.875rem;
    color: #95a5a6;
    font-style: italic;
}

.chart-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    border: none;
}

.chart-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
}

.chart-header {
    background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
    padding: 1.5rem;
    border-bottom: 1px solid #e1f5fe;
}

.chart-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #37474f;
    margin: 0;
}

.chart-title i {
    margin-right: 0.75rem;
    color: #2196F3;
}

.chart-body {
    padding: 2rem;
}

.performance-summary {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    margin-top: 1rem;
}

.performance-item {
    padding: 1rem;
}

.performance-number {
    font-size: 2rem;
    font-weight: 700;
    color: #2196F3;
    margin-bottom: 0.5rem;
}

.performance-label {
    font-size: 0.9rem;
    color: #607d8b;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
}

@media (max-width: 768px) {
    .dashboard-header {
        padding: 1.5rem 0;
    }
    
    .dashboard-title {
        font-size: 2rem;
    }
    
    .stat-card {
        margin-bottom: 1rem;
    }
    
    .stat-value {
        font-size: 2.2rem;
    }
}
</style>
@endsection