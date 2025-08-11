<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id(); // Assuming the clerk is the authenticated user

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

        // Recent orders
        $recentOrders = Order::with('customer')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        // Sales data for chart (e.g., last 7 days)
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $sales = Order::where('user_id', $userId)
                ->whereDate('created_at', $date)
                ->sum('total_amount');
            $last7Days[] = [
                'date' => $date->format('Y-m-d'),
                'sales' => $sales
            ];
        }

        return view('dashboard.index', compact(
            'todaySales',
            'todayOrdersCount',
            'weekSales',
            'monthSales',
            'totalSales',
            'topProducts',
            'recentOrders',
            'last7Days'
        ));
    }
}