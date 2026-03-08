<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Company;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class SaleController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $storeId = $user->store_id;

        //send error message when store id is null
        if ($storeId === null) {
            return back()->with('error', 'please switch to a store to view a sales.');
        }

        return view('pages.sale.index');
    }

    public function show(Sale $sale)
    {

        //prevent unathourized access
        Gate::authorize('show', $sale);

        $sale = $sale->load(['customer', 'store', 'user', 'saleItems.item']);



        return view('pages.sale.show', [
            'sale' => $sale,
        ]);
    }

    public function printReceipt($id)
    {
        try {

            $saleId = $id;
            $store_id = auth()->user()->store_id;
            $company = Company::first();

            $sale = Sale::with(['saleItems', 'store', 'customer', 'user'])
                ->withCount('saleItems')
                ->where('id', $saleId)
                ->where('store_id', $store_id)
                ->first();




            //   $orderData = Order::find($id);
            //   $branch = Branch::find($branch_id);

            // dd($orderProductsData->product);
            // Calculate paper height dynamically
            $baseHeight = 360; // Base height for fixed elements
            $itemHeight = 40;  // Estimated height per item
            $dynamicHeight = $baseHeight + ($sale->sale_items_count * $itemHeight);

            $pdf = FacadePdf::loadView('pages.shop.receipt', [
                'sale' => $sale,
                'company' => $company,

            ])->setPaper([0, 0, 227, $dynamicHeight], 'portrait');

            return $pdf->download('order-' . $sale->reference . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

}
