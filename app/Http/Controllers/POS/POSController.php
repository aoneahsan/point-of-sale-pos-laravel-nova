<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class POSController extends Controller
{
    public function index()
    {
        return view('pos.index');
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['store', 'customer', 'items.variant.product', 'payments.paymentMethod']);
        return view('pos.receipt', compact('sale'));
    }
}