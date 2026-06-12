<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = Customer::withCount('orders')
            ->withSum('orders', 'total_amount');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('contact', 'like', "%{$request->search}%");
            });
        }

        $customers      = $query->latest()->get();
        $totalCustomers = Customer::count();
        $totalRevenue   = $customers->sum('orders_sum_total_amount');

        return view('customers.index', compact('customers', 'totalCustomers', 'totalRevenue'));
    }

    public function show(Customer $customer)
    {
        $customer->load('orders.items.product', 'orders.user');
        $totalSpent  = $customer->orders->sum('total_amount');
        $orderCount  = $customer->orders->count();
        $avgOrder    = $orderCount > 0 ? $totalSpent / $orderCount : 0;
        return view('customers.show', compact('customer', 'totalSpent', 'orderCount', 'avgOrder'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
        ]);

        $customer->update($request->only('name', 'contact'));
        return redirect()->route('customers.index')->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer removed.');
    }
}
