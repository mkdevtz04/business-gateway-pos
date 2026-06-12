<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from ? Carbon::parse($request->from)->startOfDay() : now()->startOfMonth();
        $to   = $request->to   ? Carbon::parse($request->to)->endOfDay()     : now()->endOfDay();

        $orders = Order::with(['user', 'customer', 'items'])
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->get();

        $totalRevenue  = $orders->sum('total_amount');
        $totalOrders   = $orders->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $totalUnits    = $orders->sum(fn($o) => $o->items->sum('quantity'));

        // Compare with previous period of same length
        $periodDays    = $from->diffInDays($to) + 1;
        $prevFrom      = $from->copy()->subDays($periodDays);
        $prevTo        = $from->copy()->subDay();
        $prevRevenue   = Order::whereBetween('created_at', [$prevFrom, $prevTo])->sum('total_amount');
        $revenueChange = $prevRevenue > 0 ? (($totalRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;

        // Daily revenue for chart
        $dailyRevenue = Order::whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as orders_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top products
        $topProducts = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->selectRaw('products.name, SUM(order_items.quantity) as qty, SUM(order_items.subtotal) as revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->limit(8)
            ->get();

        // Revenue by payment method
        $byPayment = Order::whereBetween('created_at', [$from, $to])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('payment_method')
            ->get();

        // Top clerks
        $topClerks = Order::with('user')
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('user_id, COUNT(*) as order_count, SUM(total_amount) as revenue')
            ->groupBy('user_id')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        return view('reports.index', compact(
            'orders', 'totalRevenue', 'totalOrders', 'avgOrderValue', 'totalUnits',
            'revenueChange', 'dailyRevenue', 'topProducts', 'byPayment', 'topClerks',
            'from', 'to'
        ));
    }

    public function exportOrders(Request $request)
    {
        $from = $request->from ? Carbon::parse($request->from)->startOfDay() : now()->startOfMonth();
        $to   = $request->to   ? Carbon::parse($request->to)->endOfDay()     : now()->endOfDay();

        $orders = Order::with(['user', 'customer', 'items'])
            ->whereBetween('created_at', [$from, $to])
            ->latest()->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($orders) {
            $fh = fopen('php://output', 'w');
            fputcsv($fh, ['Order ID', 'Date', 'Customer', 'Clerk', 'Payment', 'Items', 'Total']);
            foreach ($orders as $o) {
                fputcsv($fh, [
                    '#' . str_pad($o->id, 5, '0', STR_PAD_LEFT),
                    $o->created_at->format('Y-m-d H:i'),
                    $o->customer?->name ?? 'Walk-in',
                    $o->user?->name ?? '—',
                    ucfirst($o->payment_method),
                    $o->items->sum('quantity'),
                    number_format($o->total_amount, 2),
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportProducts(Request $request)
    {
        $from = $request->from ? Carbon::parse($request->from)->startOfDay() : now()->startOfMonth();
        $to   = $request->to   ? Carbon::parse($request->to)->endOfDay()     : now()->endOfDay();

        $products = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->selectRaw('products.name, categories.name as category, SUM(order_items.quantity) as qty, SUM(order_items.subtotal) as revenue')
            ->groupBy('products.id', 'products.name', 'categories.name')
            ->orderByDesc('revenue')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="products_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($products) {
            $fh = fopen('php://output', 'w');
            fputcsv($fh, ['Product', 'Category', 'Units Sold', 'Revenue']);
            foreach ($products as $p) {
                fputcsv($fh, [$p->name, $p->category ?? '—', $p->qty, number_format($p->revenue, 2)]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }
}
