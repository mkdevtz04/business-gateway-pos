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
                'products'          => 'required|array|min:1',
                'products.*.id'     => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
                'payment_method'    => 'required|in:cash,credit',
                'customer_id'       => 'required|string',
                'customer_name'     => 'required_if:customer_id,new|string|max:255',
                'customer_contact'  => 'nullable|string|max:255',
                'discount_type'     => 'nullable|in:percent,flat',
                'discount_value'    => 'nullable|numeric|min:0',
            ]);

            // Resolve customer
            $customerId = null;
            if ($request->customer_id === 'new') {
                $customer   = Customer::create(['name' => $request->customer_name, 'contact' => $request->customer_contact]);
                $customerId = $customer->id;
            } elseif ($request->customer_id && $request->customer_id !== '') {
                $customerId = $request->customer_id;
            }

            $grossTotal = 0;
            $orderItems = [];

            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['id']);
                if ($product->quantity_available < $item['quantity']) {
                    throw ValidationException::withMessages(['products' => "Insufficient stock for {$product->name}"]);
                }
                $subtotal    = $product->price * $item['quantity'];
                $tax         = ($subtotal * $product->tax_rate) / 100;
                $grossTotal += $subtotal + $tax;
                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $product->price,
                    'tax'        => $tax,
                    'subtotal'   => $subtotal + $tax,
                    'discount'   => 0,
                ];
                $product->decrement('quantity_available', $item['quantity']);
            }

            // Apply discount
            $discountValue  = (float) ($request->discount_value ?? 0);
            $discountAmount = 0;
            if ($discountValue > 0) {
                $discountAmount = $request->discount_type === 'percent'
                    ? round($grossTotal * $discountValue / 100, 2)
                    : min($discountValue, $grossTotal);

                // Distribute discount proportionally across items
                foreach ($orderItems as &$oi) {
                    $oi['discount'] = round($discountAmount * ($oi['subtotal'] / $grossTotal), 2);
                    $oi['subtotal'] = round($oi['subtotal'] - $oi['discount'], 2);
                }
                unset($oi);
            }

            $totalAmount = $grossTotal - $discountAmount;

            $order = Order::create([
                'user_id'         => auth()->id(),
                'customer_id'     => $customerId,
                'payment_method'  => $request->payment_method,
                'total_amount'    => $totalAmount,
                'discount_amount' => $discountAmount,
                'status'          => 'completed',
            ]);

            $order->items()->createMany($orderItems);
            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Order completed successfully',
                'redirect_url' => route('orders.show', $order),
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