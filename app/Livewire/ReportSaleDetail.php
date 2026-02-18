<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Sale;
use Livewire\Component;
use App\Models\ReturnItem;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class ReportSaleDetail extends Component
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

        // Fetch the sum of the fields for the selected date range

        $query = Sale::where('store_id', auth()->user()->store_id)
            ->where('payment_status', 'paid')
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate));


        $this->subtotalSum = $query->sum('subtotal');
        $this->grandtotalSum = $query->sum('grandtotal');
        $this->discountSum = $query->sum('discount');



    }



    public function render()
    {
        $storeId = auth()->user()->store_id;

        // Fetch paginated records
        $query = Sale::with(['user', 'store'])
            ->where('store_id', $storeId)
            ->where('payment_status', 'paid')
            ->when($this->startDate, fn($query) => $query->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($query) => $query->whereDate('created_at', '<=', $this->endDate));
        $records = $query->orderBy('created_at', 'DESC')
            ->paginate($this->perPage);

        $returnItems = ReturnItem::where('store_id', $storeId)
            ->when($this->startDate, fn($query) => $query->whereDate('return_date', '>=', $this->startDate))
            ->when($this->endDate, fn($query) => $query->whereDate('return_date', '<=', $this->endDate));


        $this->subtotalSum = $query->sum('subtotal');
        $this->grandtotalSum = $query->sum('grandtotal');
        $this->discountSum = $query->sum('discount');
        $this->itemReturnedSum = $returnItems->sum('total');
        //calculate the grandtotal
        $this->grandtotalSum -= $this->itemReturnedSum;



        return view('livewire.report-sale-detail', [
            'records' => $records,
        ]);
    }
}
