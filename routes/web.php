<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\POSController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    // ── Shared (all roles) ─────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Orders — controller filters by role (clerk sees own, manager sees all)
    Route::get('/orders',             [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}',     [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/pdf', [OrderController::class, 'downloadPDF'])->name('orders.pdf');

    // Product search (used by POS)
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');

    // ── Owner + Admin: financials & monitoring ─────────────
    Route::middleware(['role:admin,owner'])->group(function () {
        Route::get('/stocks',            [StockController::class, 'index'])->name('stocks.index');
        Route::get('/stocks/export/csv', [StockController::class, 'exportCsv'])->name('stocks.export.csv');
        Route::get('/stocks/export/pdf', [StockController::class, 'exportPdf'])->name('stocks.export.pdf');

        Route::get('/reports',                 [ReportsController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/orders',   [ReportsController::class, 'exportOrders'])->name('reports.export.orders');
        Route::get('/reports/export/products', [ReportsController::class, 'exportProducts'])->name('reports.export.products');
    });

    // ── Admin only: full management ────────────────────────
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('products',   ProductController::class);
        Route::resource('customers',  CustomerController::class)->except(['create', 'store']);
        Route::resource('users',      UserController::class);
    });

    // ── Clerk: POS & personal sales ────────────────────────
    Route::middleware(['role:clerk'])->group(function () {
        Route::get('/pos',           [POSController::class, 'index'])->name('pos.index');
        Route::post('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
        Route::get('/sales',         [SalesController::class, 'index'])->name('sales.index');
        Route::get('/sales/history', [SalesController::class, 'history'])->name('sales.history');
    });
});

require __DIR__ . '/auth.php';
