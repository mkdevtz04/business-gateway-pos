<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SalesController extends Controller
{
   public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $sales = Sale::with(['product', 'user'])
            ->when($request->date, fn($query, $date) => $query->where('sale_date', $date))
            ->get();
        return view('sales.index', compact('sales'));
    }

    public function history(Request $request)
    {
        $sales = Sale::with(['product', 'user'])
            ->when($request->from, fn($q) => $q->whereDate('sale_date', '>=', $request->from))
            ->when($request->to,   fn($q) => $q->whereDate('sale_date', '<=', $request->to))
            ->latest('sale_date')
            ->get();

        return view('sales.index', compact('sales'));
    }
}
