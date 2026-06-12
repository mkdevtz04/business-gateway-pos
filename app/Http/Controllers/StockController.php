<?php

// app/Http/Controllers/StockController.php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Product::with('category');
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        $products = $query->get();
        $categories = Category::all();
        $lowStockCount = Product::where('quantity_available', '<', 10)->count();

        return view('stocks.index', compact('products', 'categories', 'lowStockCount'));
    }

    public function exportPdf()
    {
        $products = Product::with('category')->orderBy('name')->get();
        $pdf = Pdf::loadView('stocks.pdf', compact('products'));
        return $pdf->download('stock_report_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportCsv()
    {
        $products = Product::with('category')->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stock_report.csv"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Category', 'Quantity', 'Size', 'Price', 'Tax Rate']);
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->name,
                    $product->category ? $product->category->name : 'N/A',
                    $product->quantity_available,
                    $product->size ?? 'N/A',
                    $product->price,
                    $product->tax_rate,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}