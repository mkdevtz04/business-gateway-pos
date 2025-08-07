<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class POSController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        return view('pos.index', compact('products'));
    }

    public function checkout(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'products' => 'required|array|min:1',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
                'payment_method' => 'required|in:cash,credit'
            ]);

            $totalAmount = 0;
            $orderItems = [];

            // Process each product
            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['id']);
                
                // Check stock availability
                if ($product->quantity_available < $item['quantity']) {
                    // It's better to throw a ValidationException for clearer client-side error handling
                    throw ValidationException::withMessages([
                        'products' => "Insufficient stock for {$product->name}. Only {$product->quantity_available} left."
                    ]);
                }

                $subtotal = $product->price * $item['quantity'];
                $tax = ($subtotal * $product->tax_rate) / 100;
                $totalAmount += $subtotal + $tax;

                // Prepare order item data
                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'tax' => $tax,
                    'subtotal' => $subtotal + $tax
                ];

                // Update product stock
                $product->decrement('quantity_available', $item['quantity']);
            }

            // Create the main order
            $order = Order::create([
                'user_id' => auth()->id(),
                'payment_method' => $request->payment_method,
                'total_amount' => $totalAmount,
                'status' => 'completed'
            ]);

            // Attach all order items in a single query
            $order->items()->createMany($orderItems);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order completed successfully',
                'order_id' => $order->id,
                'redirect_url' => route('orders.show', $order)
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422); // 422 Unprocessable Entity is a better status code for validation errors

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}