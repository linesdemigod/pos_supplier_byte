<?php

namespace App\Livewire;

use App\Models\Item;
use Livewire\Component;
use App\Models\AuditLog;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\StoreInventory;
use Illuminate\Support\Facades\DB;

class StoreInventoryTable extends Component
{
    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    public function delete(StoreInventory $inventory)
    {

        // Get the original data before the update
        $originalData = $inventory->getOriginal();

        DB::beginTransaction();
        try {


            // delete it
            $inventory->delete();

            // Prepare audit trail data
            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => request()->ip(),
                'description' => 'store inventory deletion',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            session()->flash('message', 'Store inventory deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'An error ocurred while deleting ');
        }
    }

    public function render()
    {
        $storeId = auth()->user()->store_id;

        $inventories = StoreInventory::with('item.category')
            ->where('store_id', $storeId)
            ->search($this->search)
            ->latest()
            ->paginate($this->perPage);



        return view('livewire.store-inventory-table', [
            'inventories' => $inventories,
        ]);
    }
}
