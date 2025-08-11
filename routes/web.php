<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\POSController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');



    // Admin Routes
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('users', UserController::class);
        Route::get('/admin/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/admin/orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');


    });

    // Clerk Routes
    Route::middleware(['role:clerk'])->group(function () {

        // Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
        Route::post('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
        Route::resource('orders', OrderController::class)->only(['index', 'show', 'store', 'create']);
        Route::get('/sales/history', [SalesController::class, 'history'])->name('sales.history');
        Route::get('sales', [SalesController::class, 'index'])->name('sales.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');



        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}/pdf', [OrderController::class, 'downloadPDF'])->name('orders.pdf');
    });
});

require __DIR__ . '/auth.php';
