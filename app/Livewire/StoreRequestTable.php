<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AuditLog;
use App\Models\ItemRequest;
use Illuminate\Support\Str;
use App\Models\StoreRequest;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class StoreRequestTable extends Component
{
    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;
    public $status = 'all';

    protected $paginationTheme = 'bootstrap';


    public function delete(StoreRequest $item)
    {

        if (Str::lower($item->status) == 'completed') {
            session()->flash('message', 'Request completed successfully');
            return;
        }
        // Get the original data before the update
        $originalData = $item->getOriginal();

        DB::beginTransaction();
        try {

            // delete it
            $item->delete();

            // Prepare audit trail data
            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => request()->ip(),
                'description' => 'store request deletion',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            session()->flash('message', 'Requested Item deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'An error ocurred while deleting ');
        }
    }

    public function render()
    {
        $user = auth()->user();
        $filterColumn = $user->store_id ? 'store_id' : 'warehouse_id';
        $filterValue = $user->store_id ?: $user->warehouse_id;

        $items = StoreRequest::with(['approvedBy', 'requestedBy', 'store', 'warehouse', 'user'])
            ->where($filterColumn, $filterValue)
            // ->where('status', '=', $this->status)
            ->when($this->status !== 'all', function ($query) {
                $query->where('status', $this->status);
            })
            ->search($this->search)
            ->latest()
            ->paginate($this->perPage);



        return view('livewire.store-request-table', [
            'items' => $items,
        ]);
    }
}
