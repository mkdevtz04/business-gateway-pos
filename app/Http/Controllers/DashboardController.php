<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role;

        if ($role === 'admin')  return $this->adminDashboard();
        if ($role === 'owner')  return $this->ownerDashboard();

        return $this->clerkDashboard(auth()->user()->id);
    }
    
    private function adminDashboard()
    {
        // Admin sees all data across all users/clerks
        
        // Overall Statistics
        $totalSales = Order::sum('total_amount');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalClerks = User::where('role', 'clerk')->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        
        // Sales trend for last 30 days
        $salesTrend = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $sales = Order::whereDate('created_at', $date)->sum('total_amount');
            $orders = Order::whereDate('created_at', $date)->count();
            $salesTrend[] = [
                'date' => $date->format('M d'),
                'sales' => floatval($sales),
                'orders' => $orders
            ];
        }
        
        // Monthly sales comparison (last 12 months)
        $monthlySales = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $sales = Order::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total_amount');
            $monthlySales[] = [
                'month' => $date->format('M Y'),
                'sales' => floatval($sales)
            ];
        }
        
        // Sales by category
        $salesByCategory = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                DB::raw('COALESCE(categories.name, "Uncategorized") as category_name'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_sales'),
                DB::raw('SUM(order_items.quantity) as total_quantity')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sales')
            ->get();
        
        // Top performing clerks
        $topClerks = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'users.name',
                DB::raw('SUM(orders.total_amount) as total_sales'),
                DB::raw('COUNT(orders.id) as total_orders')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();
        
        // Top selling products
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();
        
        // Hourly sales pattern (for today)
        $hourlySales = DB::table('orders')
            ->whereDate('created_at', Carbon::today())
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('SUM(total_amount) as sales'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        
        // Fill missing hours with 0
        $hourlyData = [];
        for ($i = 0; $i < 24; $i++) {
            $existing = $hourlySales->firstWhere('hour', $i);
            $hourlyData[] = [
                'hour' => sprintf('%02d:00', $i),
                'sales' => $existing ? floatval($existing->sales) : 0,
                'orders' => $existing ? $existing->orders : 0
            ];
        }
        
        // Clerk performance table — today vs all time
        $clerkPerformance = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('users.role', 'clerk')
            ->select(
                'users.id',
                'users.name',
                DB::raw('SUM(orders.total_amount) as total_revenue'),
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(CASE WHEN DATE(orders.created_at) = CURDATE() THEN orders.total_amount ELSE 0 END) as today_revenue'),
                DB::raw('COUNT(CASE WHEN DATE(orders.created_at) = CURDATE() THEN 1 END) as today_orders')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('today_revenue')
            ->get();

        return view('dashboard.index', compact(
            'totalSales',
            'totalOrders',
            'totalProducts',
            'totalClerks',
            'averageOrderValue',
            'salesTrend',
            'monthlySales',
            'salesByCategory',
            'topClerks',
            'topProducts',
            'hourlyData',
            'clerkPerformance'
        ));
    }
    
    private function ownerDashboard()
    {
        // ── Revenue KPIs ───────────────────────────────────────
        $allTimeRevenue   = Order::sum('total_amount');
        $thisMonthRevenue = Order::whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year)
                                 ->sum('total_amount');
        $lastMonthRevenue = Order::whereMonth('created_at', now()->subMonth()->month)
                                 ->whereYear('created_at', now()->subMonth()->year)
                                 ->sum('total_amount');
        $revenueChange    = $lastMonthRevenue > 0
            ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        $thisMonthOrders  = Order::whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year)
                                 ->count();
        $todayRevenue     = Order::whereDate('created_at', today())->sum('total_amount');
        $activeStaffToday = Order::whereDate('created_at', today())
                                 ->distinct('user_id')->count('user_id');

        // ── Revenue trend (last 30 days) ───────────────────────
        $revenueTrend = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $revenueTrend[] = [
                'date'   => $date->format('M d'),
                'amount' => (float) Order::whereDate('created_at', $date)->sum('total_amount'),
            ];
        }

        // ── Payment method breakdown ───────────────────────────
        $paymentBreakdown = DB::table('orders')
            ->select(DB::raw('payment_method, COUNT(*) as count, SUM(total_amount) as revenue'))
            ->groupBy('payment_method')
            ->get();

        // ── Staff performance (this month) ─────────────────────
        $staffPerformance = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('users.role', 'clerk')
            ->select(
                'users.id',
                'users.name',
                DB::raw('SUM(orders.total_amount) as month_revenue'),
                DB::raw('COUNT(orders.id) as month_orders'),
                DB::raw('SUM(CASE WHEN DATE(orders.created_at) = CURDATE() THEN orders.total_amount ELSE 0 END) as today_revenue'),
                DB::raw('COUNT(CASE WHEN DATE(orders.created_at) = CURDATE() THEN 1 END) as today_orders'),
                DB::raw('AVG(orders.total_amount) as avg_order')
            )
            ->whereMonth('orders.created_at', now()->month)
            ->whereYear('orders.created_at', now()->year)
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('month_revenue')
            ->get();

        $totalStaff = User::where('role', 'clerk')->count();

        // ── Stock health ───────────────────────────────────────
        $stockHealth = [
            'in_stock'  => Product::where('quantity_available', '>=', 10)->count(),
            'low_stock' => Product::whereBetween('quantity_available', [1, 9])->count(),
            'out_stock' => Product::where('quantity_available', '<=', 0)->count(),
            'total'     => Product::count(),
        ];

        // ── Recent orders (last 8) ─────────────────────────────
        $recentOrders = Order::with(['user', 'customer'])
            ->latest()->limit(8)->get();

        return view('dashboard.owner', compact(
            'allTimeRevenue', 'thisMonthRevenue', 'lastMonthRevenue', 'revenueChange',
            'thisMonthOrders', 'todayRevenue', 'activeStaffToday', 'totalStaff',
            'revenueTrend', 'paymentBreakdown', 'staffPerformance',
            'stockHealth', 'recentOrders'
        ));
    }

    private function clerkDashboard($userId)
    {
        // Clerk sees only their own data
        
        // Personal Statistics
        $totalSales = Order::where('user_id', $userId)->sum('total_amount');
        $totalOrders = Order::where('user_id', $userId)->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        
        // Today's performance
        $todaySales = Order::where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');
        $todayOrders = Order::where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->count();
        
        // Sales trend for last 14 days
        $salesTrend = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $sales = Order::where('user_id', $userId)
                ->whereDate('created_at', $date)
                ->sum('total_amount');
            $orders = Order::where('user_id', $userId)
                ->whereDate('created_at', $date)
                ->count();
            $salesTrend[] = [
                'date' => $date->format('M d'),
                'sales' => floatval($sales),
                'orders' => $orders
            ];
        }
        
        // Weekly comparison (last 8 weeks)
        $weeklySales = [];
        for ($i = 7; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();
            $sales = Order::where('user_id', $userId)
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->sum('total_amount');
            $weeklySales[] = [
                'week' => 'Week ' . (8 - $i),
                'sales' => floatval($sales)
            ];
        }
        
        // Sales by category (clerk's sales only)
        $salesByCategory = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.user_id', $userId)
            ->select(
                DB::raw('COALESCE(categories.name, "Uncategorized") as category_name'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_sales'),
                DB::raw('SUM(order_items.quantity) as total_quantity')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sales')
            ->get();
        
        // Top selling products by this clerk
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.user_id', $userId)
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(8)
            ->get();
        
        // Hourly performance pattern (average for last 7 days)
        $hourlyPattern = DB::table('orders')
            ->where('user_id', $userId)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('AVG(total_amount) as avg_sales'),
                DB::raw('COUNT(*) as total_orders')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        
        // Fill missing hours with 0
        $hourlyData = [];
        for ($i = 0; $i < 24; $i++) {
            $existing = $hourlyPattern->firstWhere('hour', $i);
            $hourlyData[] = [
                'hour' => sprintf('%02d:00', $i),
                'avg_sales' => $existing ? floatval($existing->avg_sales) : 0,
                'orders' => $existing ? $existing->total_orders : 0
            ];
        }
        
        return view('dashboard.clerk', compact(
            'totalSales',
            'totalOrders',
            'averageOrderValue',
            'todaySales',
            'todayOrders',
            'salesTrend',
            'weeklySales',
            'salesByCategory',
            'topProducts',
            'hourlyData'
        ));
    }
}