<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    // public function index()
    // {
    //     $orders = Order::with(['product', 'customer', 'user'])->get();
    //     return view('orders.index', compact('orders'));
    // }

    public function index()
    {
        // Fetch all orders, newest first, and paginate them.
        // Eager load the 'user' relationship to prevent N+1 query problems.
        $orders = Order::with('user')
            ->latest()
            ->paginate(15); // Show 15 orders per page

        return view('orders.index', compact('orders'));
    }



    public function show(Order $order)
    {
        // die('This is the show method');
        // Thanks to Laravel's route model binding, the correct Order instance
        // is automatically injected.

        // Eager load the relationships: items and the product related to each item. [1, 2]
        $order->load(['items.product']);

        return view('orders.show', compact('order'));
    }


    public function create()
    {
        return view('orders.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'discount' => 'nullable|numeric|min:0|max:100',
            'payment_method' => 'nullable|string',
            'customer_name' => 'nullable|string|max:255',
            'customer_contact' => 'nullable|string|max:255',
        ]);

        // Get the product to calculate totals
        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity;
        $discount = $request->discount ?? 0;

        // Calculate totals
        $subtotal = $product->price * $quantity;
        $discountAmount = ($discount / 100) * $subtotal;
        $tax = (($product->tax_rate ?? 0) / 100) * $subtotal;
        $total = $subtotal - $discountAmount + $tax;

        // Create the order (without product_id)
        $order = Order::create([
            'user_id' => auth()->id(),
            'total_amount' => $total,
            'payment_method' => $request->payment_method,
            'status' => 'completed', // or whatever default status you want
            'customer_name' => $request->customer_name ?? null,
            'customer_contact' => $request->customer_contact ?? null,
        ]);

        // Create the order item (this is where product_id goes)
        $order->orderItems()->create([
            'product_id' => $request->product_id,
            'quantity' => $quantity,
            'price' => $product->price, // Price at time of order
            'subtotal' => $subtotal,
            'discount' => $discountAmount,
            'tax' => $tax,
        ]);

        // Update product quantity
        $product->decrement('quantity_available', $quantity);

        return redirect()->route('orders.index')->with('success', 'Order created successfully!');
    }
}
