<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Order $order)
    {
        $order->load(['product', 'customer', 'user']);
        $pdf = Pdf::loadView('receipts.show', compact('order'));
        return $pdf->download("receipt_{$order->id}.pdf");
    }
}
