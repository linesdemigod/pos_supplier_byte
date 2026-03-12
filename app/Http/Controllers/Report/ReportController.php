<?php

namespace App\Http\Controllers\Report;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {


        return view('pages.report.index');
    }

    public function staff()
    {


        return view('pages.report.staff.staff');
    }

    public function customer()
    {


        return view('pages.report.customer.customer');
    }

    public function warehouse()
    {


        return view('pages.report.warehouse.warehouse');
    }

    public function purchaseHistory(Customer $customer)
    {
        $records = Sale::select('customers.name', DB::raw('SUM(sales.grandtotal) AS grandtotal'), DB::raw('COUNT(sales.customer_id) AS visits'))

            ->join('customers', 'customers.id', 'sales.customer_id')
            ->where('sales.customer_id', $customer->id)
            ->where('sales.payment_status', '=', 'paid')
            ->groupBy('customers.id', 'customers.name')
            ->get();

        $frequentItemPurchase = SaleItem::select('items.name', DB::raw('SUM(sale_items.quantity) AS quantity'), )
            ->join('items', 'items.id', 'sale_items.item_id')
            ->join('sales', 'sales.id', 'sale_items.sale_id')
            ->where('sales.customer_id', $customer->id)
            ->where('sales.payment_status', '=', 'paid')
            ->groupBy('items.id', 'items.name')
            ->orderBy('quantity', 'desc')
            ->limit(5)
            ->get();


        return view('pages.report.customer.history', [
            'records' => $records,
            'frequentItemPurchase' => $frequentItemPurchase,
            'customer' => $customer
        ]);
    }

    public function shift()
    {
        $dates = $this->getCurrentMonthRange();

        return view('pages.report.staff.shift', [
            'dates' => $dates
        ]);
    }

    private function getCurrentMonthRange(): array
    {
        // $startOfMonth = Carbon::now()->startOfMonth();
        // $endOfMonth = Carbon::now()->endOfMonth();

        return $monthRange = [
            'start' => now()->startOfMonth()->format('Y-m-d'),
            'end' => now()->endOfMonth()->format('Y-m-d')
        ];
        ;
    }
}
