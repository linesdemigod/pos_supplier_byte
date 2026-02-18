<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Store;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\TransferOrder;
use Illuminate\Support\Facades\DB;

class ReportWarehouseInventory extends Component
{

    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    public $startDate = '';
    public $endDate = '';
    public $stores;
    public $selectedStoreId;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $today = Carbon::now()->toDateString();
        $this->startDate = $today;
        $this->endDate = $today;

        $this->stores = Store::latest()->get();
        $this->selectedStoreId = $this->stores[0]->id;




    }


    public function getSaleDetail()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'selectedStoreId' => 'required|exists:stores,id'
        ], [
            'selectedStoreId.required' => 'The store is required',
            'selectedStoreId.exists' => 'The selected store does not exist'
        ]);

        $user = auth()->user();
        $warehouseId = $user->warehouse_id;


        $rcords = TransferOrder::select('transfer_order_details.quantity', 'transfer_order_details.price', 'transfer_order_details.total', 'items.name')
            ->leftJoin('transfer_order_details', 'transfer_order_details.transfer_order_id', '=', 'transfer_orders.id')
            ->leftJoin('items', 'items.id', '=', 'transfer_order_details.item_id')
            ->where('transfer_order_details.created_at', '>=', $this->startDate)
            ->whereDate('transfer_order_details.created_at', '<=', $this->endDate)
            ->whereDate('transfer_orders.warehouse_id', $warehouseId)
            ->where('transfer_orders.store_id', $this->selectedStoreId);

    }

    public function render()
    {
        $user = auth()->user();
        $warehouseId = $user->warehouse_id;

        $records = TransferOrder::select('transfer_order_details.quantity', 'transfer_order_details.price', 'transfer_order_details.total', 'items.name')
            ->leftJoin('transfer_order_details', 'transfer_order_details.transfer_order_id', '=', 'transfer_orders.id')
            ->leftJoin('items', 'items.id', '=', 'transfer_order_details.item_id')
            ->where('transfer_order_details.created_at', '>=', $this->startDate)
            ->whereDate('transfer_order_details.created_at', '<=', $this->endDate)
            ->whereDate('transfer_orders.warehouse_id', $warehouseId)
            ->where('transfer_orders.store_id', $this->selectedStoreId)
            ->paginate($this->perPage);



        return view('livewire.report-warehouse-inventory', [
            'records' => $records,
        ]);
    }
}
