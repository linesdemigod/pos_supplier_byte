<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AuditLog;
use App\Models\Customer;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class CustomerTable extends Component
{

    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';


    public function delete(Customer $customer)
    {

        // Get the original data before the update
        $originalData = $customer->getOriginal();

        DB::beginTransaction();
        try {


            // delete it
            $customer->delete();

            // Prepare audit trail data
            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => request()->ip(),
                'description' => 'customer deletion',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            session()->flash('message', 'Customer deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'An error ocurred while deleting ');
        }
    }
    public function render()
    {
        $customers = Customer::with('store')
            ->where('store_id', auth()->user()->store_id)
            ->search($this->search)
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.customer-table', [
            'customers' => $customers,
        ]);
    }
}
