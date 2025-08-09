<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer; // <-- Import Customer model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class POSController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        $customers = Customer::orderBy('name')->get(); // <-- Fetch customers
        
        // Pass both to the view
        return view('pos.index', compact('products', 'customers'));
    }

    public function checkout(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'products' => 'required|array|min:1',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
                'payment_method' => 'required|in:cash,credit',
                'customer_id' => 'required|string', // can be an ID or "new"
                'customer_name' => 'required_if:customer_id,new|string|max:255',
                'customer_contact' => 'nullable|string|max:255',
            ]);

            // === Customer Logic ===
            $customerId = null;
            if ($request->customer_id === 'new') {
                $customer = Customer::create([
                    'name' => $request->customer_name,
                    'contact' => $request->customer_contact,
                ]);
                $customerId = $customer->id;
            } else {
                $customerId = $request->customer_id;
            }
            // ======================

            $totalAmount = 0;
            $orderItems = [];

            foreach ($request->products as $item) {
                // ... (product processing logic remains the same)
                $product = Product::findOrFail($item['id']);
                if ($product->quantity_available < $item['quantity']) {
                    throw ValidationException::withMessages(['products' => "Insufficient stock for {$product->name}"]);
                }
                $subtotal = $product->price * $item['quantity'];
                $tax = ($subtotal * $product->tax_rate) / 100;
                $totalAmount += $subtotal + $tax;
                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'tax' => $tax,
                    'subtotal' => $subtotal + $tax
                ];
                $product->decrement('quantity_available', $item['quantity']);
            }

            // Create the main order
            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_id' => $customerId, // <-- Assign the customer ID
                'payment_method' => $request->payment_method,
                'total_amount' => $totalAmount,
                'status' => 'completed'
            ]);

            $order->items()->createMany($orderItems);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order completed successfully',
                'redirect_url' => route('orders.show', $order)
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}