<?php

namespace App\Livewire;

use Carbon\Carbon;

use App\Models\Sale;
use App\Models\User;
use Livewire\Component;
use App\Models\ReturnItem;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ReportStaffSales extends Component
{
    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    public $startDate = '';
    public $endDate = '';
    public $users;
    public $selectedUserId = null;

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
        $storeId = auth()->user()->store_id;
        $this->users = User::where('store_id', $storeId)->get();
        $this->selectedUserId = $this->users[0]->id ?? null;


    }

    public function getSaleDetail()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'selectedUserId' => 'required|exists:users,id'
        ], [
            'selectedUserId.required' => 'The staff is required',
            'selectedUserId.exists' => 'The selected staff does not exist'
        ]);



        $query = Sale::where('store_id', auth()->user()->store_id)
            ->where('payment_status', 'paid')
            ->where('user_id', $this->selectedUserId)
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
            ->where('user_id', $this->selectedUserId)
            ->when($this->startDate, fn($query) => $query->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($query) => $query->whereDate('created_at', '<=', $this->endDate));
        $records = $query->orderBy('created_at', 'DESC')
            ->paginate($this->perPage);

        $returnItems = ReturnItem::where('store_id', $storeId)
            ->when($this->startDate, fn($query) => $query->whereDate('return_date', '>=', $this->startDate))
            ->when($this->endDate, fn($query) => $query->whereDate('return_date', '<=', $this->endDate));

        //fetch use




        $this->subtotalSum = $query->sum('subtotal');
        $this->grandtotalSum = $query->sum('grandtotal');
        $this->discountSum = $query->sum('discount');
        $this->itemReturnedSum = $returnItems->sum('total');
        //calculate the grandtotal
        $this->grandtotalSum -= $this->itemReturnedSum;

        return view('livewire.report-staff-sales', [
            'records' => $records,

        ]);
    }
}
