<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Sale;
use Livewire\Component;
use App\Models\ReturnItem;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ReportSalesItem extends Component
{

    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;


    public $startDate = '';
    public $endDate = '';
    // public $records = [];


    protected $paginationTheme = 'bootstrap';
    public function mount()
    {
        $today = Carbon::now()->toDateString();
        $this->startDate = $today;
        $this->endDate = $today;
    }

    public function getSaleDetail()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);

        $storeId = auth()->user()->store_id;

        // Aggregate sales details
        $query = DB::table('sales')
            ->select(
                DB::raw('SUM(sale_items.quantity) AS quantity'),
                DB::raw('SUM(sale_items.total) AS subtotal'),
                DB::raw('COALESCE(SUM(return_items.item_id), 0) AS item_returned'),
                'items.name AS name',
                'items.id AS itemId'
            )
            ->leftJoin('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->leftJoin('items', 'sale_items.item_id', '=', 'items.id')
            ->leftJoin('return_items', 'return_items.item_id', '=', 'items.id')
            ->where('sales.store_id', $storeId)
            ->where('sales.payment_status', 'paid')
            ->where('items.name', 'LIKE', '%' . $this->search . '%')
            // ->when($this->startDate, fn($query) => $query->whereDate('sale_items.created_at', '>=', $this->startDate))
            // ->when($this->endDate, fn($query) => $query->whereDate('sale_items.created_at', '<=', $this->endDate))
            ->groupBy('name', 'items.id');


    }

    public function render()
    {
        $storeId = auth()->user()->store_id;

        // Paginated sales records
        $records = DB::table('sales')
            ->select(
                DB::raw('SUM(sale_items.quantity) AS quantity'),
                DB::raw('SUM(sale_items.total) AS subtotal'),
                DB::raw('COALESCE(SUM(return_items.item_id), 0) AS item_returned'),
                'items.name AS name',
                'items.id AS itemId'
            )
            ->leftJoin('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->leftJoin('items', 'sale_items.item_id', '=', 'items.id')
            ->leftJoin('return_items', 'return_items.item_id', '=', 'items.id')
            ->where('sales.store_id', $storeId)
            ->where('payment_status', 'paid')
            ->where('items.name', 'LIKE', '%' . $this->search . '%')
            // ->when($this->startDate, fn($query) => $query->whereDate('sale_items.created_at', '>=', $this->startDate))
            // ->when($this->endDate, fn($query) => $query->whereDate('sale_items.created_at', '<=', $this->endDate))
            ->groupBy('name', 'items.id')
            ->orderBy('sale_items.created_at', 'DESC')
            ->paginate($this->perPage);

        // Aggregate returned items
        $returnItems = ReturnItem::where('store_id', $storeId)
            ->when($this->startDate, fn($query) => $query->whereDate('return_date', '>=', $this->startDate))
            ->when($this->endDate, fn($query) => $query->whereDate('return_date', '<=', $this->endDate));


        return view('livewire.report-sales-item', [
            'records' => $records,
        ]);
    }

}
