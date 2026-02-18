<?php

namespace App\Livewire;

use App\Models\QuantityHistory;
use Livewire\Component;

use Livewire\Attributes\Url;
use Livewire\WithPagination;

class ReportStockHistory extends Component
{

    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $user = auth()->user();
        $warehouseId = $user->warehouse_id;
        $storeId = $user->store_id;

        $items = QuantityHistory::with(['item', 'user', 'store', 'warehouse'])
            ->when($warehouseId !== null, function ($query) use ($warehouseId) {
                $query->where('warehouse_id', '=', $warehouseId);
            })
            ->when($storeId !== null, function ($query) use ($storeId) {
                $query->where('store_id', '=', $storeId);
            })
            ->search($this->search)
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.report-stock-history', [
            'items' => $items,
            'showStoreColumn' => $storeId,
            'showWarehouseColumn' => $warehouseId,
        ]);
    }
}
