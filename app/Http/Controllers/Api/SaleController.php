<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {

        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', 1);
        $search = $request->input('q');

        $orders = Sale::with(['customer', 'user', 'saleItems.item'])
            // ->whereIn('payment_status', ['paid', 'credit'])
            ->search($search)
            ->whereIn('payment_status', ['paid', 'voided', 'void'])
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);


        return response()->json([
            'orders' => $orders->items(),
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'total' => $orders->total(),
            'per_page' => $orders->perPage(),
        ], 200);
    }
}
