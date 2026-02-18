<?php

namespace App\Livewire;

use App\Models\ReturnItem;
use Livewire\Component;
use App\Models\AuditLog;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ReturnItemTable extends Component
{
    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';


    public function delete(ReturnItem $item)
    {

        // Get the original data before the update
        $originalData = $item->getOriginal();

        $user = auth()->user();
        $userId = $user->id;
        $warehouseId = $user->warehouse_id;
        $storeId = $user->store_id;

        DB::beginTransaction();
        try {


            // delete it
            $item->delete();

            // Prepare audit trail data
            $auditTrail = [
                'user_id' => $userId,
                'store_id' => $storeId,
                'warehouse_id' => $warehouseId,
                'ip_address' => request()->ip(),
                'description' => 'item deletion',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            session()->flash('message', 'Return Item deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'An error ocurred while deleting ');
        }
    }
    public function render()
    {
        $user = auth()->user();
        $warehouseId = $user->warehouse_id;
        $storeId = $user->store_id;

        $items = ReturnItem::with(['item', 'store', 'warehouse'])
            ->when($warehouseId !== null, function ($query) use ($warehouseId) {
                $query->where('warehouse_id', '=', $warehouseId);
            })
            ->when($storeId !== null, function ($query) use ($storeId) {
                $query->where('store_id', '=', $storeId);
            })
            ->search($this->search)
            ->latest()
            ->paginate($this->perPage);

        // Determine column visibility


        return view('livewire.return-item-table', [
            'items' => $items,
            'showStoreColumn' => $storeId,
            'showWarehouseColumn' => $warehouseId,
        ]);
    }

}
