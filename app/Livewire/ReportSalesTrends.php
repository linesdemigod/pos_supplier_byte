<?php

namespace App\Livewire;

use App\Models\Sale;
use Livewire\Component;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class ReportSalesTrends extends Component
{
    public $bestSelling = [];
    public $worstSelling = [];

    public function mount()
    {
        $storeId = auth()->user()->store_id;

        $this->bestSelling = Sale::where('store_id', $storeId)
            ->where('payment_status', 'paid')
            ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->join('items', 'sale_items.item_id', '=', 'items.id')
            ->select('items.id', 'items.name', DB::raw('SUM(sale_items.quantity) as total_quantity'))
            ->groupBy('items.id', 'items.name')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();



        // $this->worstSelling = Sale::where('store_id', $storeId)
        //     ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
        //     ->join('items', 'sale_items.item_id', '=', 'items.id')
        //     ->select('items.id', 'items.name', DB::raw('SUM(sale_items.quantity) as total_quantity'))
        //     ->groupBy('items.id', 'items.name')
        //     ->orderBy('total_quantity')
        //     ->take(5)
        //     ->get();

    }

    public function render()
    {
        return view('livewire.report-sales-trends');
    }
}
