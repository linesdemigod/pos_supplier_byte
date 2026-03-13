<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\AuditLog;
use App\Models\CashMovement;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class CashMovementManager extends Component
{

    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';


    public function delete(CashMovement $cashMovement)
    {

        // Get the original data before the update
        $originalData = $cashMovement->getOriginal();

        DB::beginTransaction();
        try {


            // delete it
            $cashMovement->delete();

            // Prepare audit trail data
            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => request()->ip(),
                'description' => 'cash Movement deletion',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            session()->flash('message', 'Cash movement deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'An error ocurred while deleting ');
        }
    }


    public function approval(CashMovement $cashMovement)
    {

        // Get the original data before the update
        $originalData = $cashMovement->getOriginal();

        DB::beginTransaction();
        try {


            // delete it
            $cashMovement->update(
                [
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'updated_at' => now(),
                ]
            );

            // Prepare audit trail data
            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => request()->ip(),
                'description' => 'cash Movement approval',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            session()->flash('message', 'Cash movement approved successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'An error ocurred while approving ');
        }
    }


    public function render()
    {
        $records = CashMovement::with(['approvedBy', 'user'])
            ->search($this->search)
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.cash-movement-manager', [
            'records' => $records
        ]);
    }
}
