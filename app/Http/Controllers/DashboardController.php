<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        
        // Today's sales
        $today = Carbon::today();
        $todaySales = Order::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->sum('total_amount');
        
        $todayOrdersCount = Order::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->count();
        
        // This week's sales
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $weekSales = Order::where('user_id', $userId)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->sum('total_amount');
        
        // This month's sales
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $monthSales = Order::where('user_id', $userId)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('total_amount');
        
        // Total sales all time for the clerk
        $totalSales = Order::where('user_id', $userId)->sum('total_amount');
        
        // Top selling products (based on quantity sold by this clerk)
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.user_id', $userId)
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
        
        // Sales by category (for pie chart)
        $salesByCategory = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.user_id', $userId)
            ->select(
                DB::raw('COALESCE(categories.name, "Uncategorized") as category_name'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_sales')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sales')
            ->get();
        
        // Product quantity distribution (for donut chart)
        $productQuantityDistribution = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.user_id', $userId)
            ->select(
                DB::raw('COALESCE(categories.name, "Uncategorized") as category_name'),
                DB::raw('SUM(order_items.quantity) as total_quantity')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_quantity')
            ->get();
        
        // Recent orders
        $recentOrders = Order::with('customer')
            ->where('user_id', $userId)
            ->latest()
            ->take(10)
            ->get();
        
        // Sales data for chart (last 7 days)
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $sales = Order::where('user_id', $userId)
                ->whereDate('created_at', $date)
                ->sum('total_amount');
            $last7Days[] = [
                'date' => $date->format('M d'),
                'sales' => floatval($sales)
            ];
        }
        
        // Monthly comparison data (current month vs last month)
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $lastMonthSales = Order::where('user_id', $userId)
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('total_amount');
        
        // Calculate growth percentages
        $weeklyGrowth = $this->calculateGrowthPercentage($weekSales, $lastMonthSales);
        $monthlyGrowth = $this->calculateGrowthPercentage($monthSales, $lastMonthSales);
        
        // Average order value
        $totalOrdersCount = Order::where('user_id', $userId)->count();
        $averageOrderValue = $totalOrdersCount > 0 ? $totalSales / $totalOrdersCount : 0;
        
        return view('dashboard.index', compact(
            'todaySales',
            'todayOrdersCount',
            'weekSales',
            'monthSales',
            'totalSales',
            'topProducts',
            'recentOrders',
            'last7Days',
            'salesByCategory',
            'productQuantityDistribution',
            'weeklyGrowth',
            'monthlyGrowth',
            'averageOrderValue'
        ));
    }
    
    private function calculateGrowthPercentage($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }
}