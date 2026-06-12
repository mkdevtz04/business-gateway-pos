<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
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

    public function index(\Illuminate\Http\Request $request)
    {
        $isManager = in_array(auth()->user()->role, ['admin', 'owner']);
        $query     = Order::with(['user', 'customer'])->latest();

        if (!$isManager) {
            $query->where('user_id', auth()->id());
        } else {
            // Admin/owner filters
            if ($request->clerk_id)  $query->where('user_id', $request->clerk_id);
            if ($request->payment)   $query->where('payment_method', $request->payment);
            if ($request->from)      $query->whereDate('created_at', '>=', $request->from);
            if ($request->to)        $query->whereDate('created_at', '<=', $request->to);
        }

        $orders = $query->paginate(20)->withQueryString();
        $clerks = $isManager
            ? \App\Models\User::whereIn('role', ['clerk'])->orderBy('name')->get()
            : collect();

        // Summary stats for admin/owner toolbar
        $todayRevenue  = $isManager ? Order::whereDate('created_at', today())->sum('total_amount') : 0;
        $todayOrders   = $isManager ? Order::whereDate('created_at', today())->count() : 0;
        $weekRevenue   = $isManager ? Order::whereBetween('created_at', [now()->startOfWeek(), now()])->sum('total_amount') : 0;

        return view('orders.index', compact('orders', 'clerks', 'isManager', 'todayRevenue', 'todayOrders', 'weekRevenue'));
    }



   public function show(Order $order)
{
    $user = auth()->user();
    $role = strtolower($user->role); // normalize case

    if ($role === 'clerk' && $order->user_id !== $user->id) {
        abort(403, 'Unauthorized to view this order.');
    }

    // admins & owners can view all orders
    $order->load(['items.product', 'user', 'customer']);

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

    public function downloadPDF(Order $order)
    {
        // Load relationships for invoice
        $order->load(['items.product', 'customer', 'user']);

        // Render invoice view as PDF
        $pdf = Pdf::loadView('orders.invoice-pdf', compact('order'))
            ->setPaper('A4', 'portrait');

        // Return as download
        return $pdf->download('invoice-' . $order->id . '.pdf');
    }
}
