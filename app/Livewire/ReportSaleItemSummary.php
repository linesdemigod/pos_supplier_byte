<?php

namespace App\Livewire;


use App\Models\ReturnItem;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;


class ReportSaleItemSummary extends Component
{

    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    public $startDate = '';
    public $endDate = '';
    // public $records = [];

    public $subtotalSum = 0;
    public $grandtotalSum = 0;
    public $discountSum = 0;
    public $itemReturnedSum = 0;

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
                'items.price AS price',
                DB::raw('SUM(sale_items.total) AS subtotal'),
                'items.name AS name',
                'items.id AS itemId'
            )
            ->leftJoin('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->leftJoin('items', 'sale_items.item_id', '=', 'items.id')
            ->leftJoin('return_items', 'return_items.item_id', '=', 'items.id')
            ->where('sales.store_id', $storeId)
            ->where('sales.payment_status', 'paid')
            ->when($this->startDate, fn($query) => $query->whereDate('sale_items.created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($query) => $query->whereDate('sale_items.created_at', '<=', $this->endDate))
            ->groupBy('name', 'items.id', 'price');


    }

    public function render()
    {
        $storeId = auth()->user()->store_id;

        // Paginated sales records
        $records = DB::table('sales')
            ->select(
                DB::raw('SUM(sale_items.quantity) AS quantity'),
                'sale_items.price AS price',
                DB::raw('SUM(sale_items.total) AS subtotal'),
                'items.name AS name',
                'items.id AS itemId'
            )
            ->leftJoin('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->leftJoin('items', 'sale_items.item_id', '=', 'items.id')
            ->leftJoin('return_items', 'return_items.item_id', '=', 'items.id')
            ->where('sales.store_id', $storeId)
            ->where('payment_status', 'paid')
            ->when($this->startDate, fn($query) => $query->whereDate('sale_items.created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($query) => $query->whereDate('sale_items.created_at', '<=', $this->endDate))
            ->groupBy('name', 'items.id', 'price')
            ->orderBy('sale_items.created_at', 'DESC')
            ->paginate($this->perPage);

        // Aggregate returned items
        $returnItems = ReturnItem::where('store_id', $storeId)
            ->when($this->startDate, fn($query) => $query->whereDate('return_date', '>=', $this->startDate))
            ->when($this->endDate, fn($query) => $query->whereDate('return_date', '<=', $this->endDate));

        return view('livewire.report-sale-item-summary', [
            'records' => $records,
        ]);
    }
}
