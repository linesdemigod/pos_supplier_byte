<?php

namespace App\Livewire;

use App\Models\Sale;
use Livewire\Component;
use App\Models\AuditLog;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class SaleTable extends Component
{

    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    public function void(Sale $sale)
    {

        // Get the original data before the update
        $originalData = $sale->getOriginal();

        DB::beginTransaction();
        try {


            // void it
            $sale->update([
                'payment_status' => 'voided',
            ]);

            // Prepare audit trail data
            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => request()->ip(),
                'description' => 'sales voided',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            session()->flash('message', 'sale voided successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'An error ocurred while voiding ');
        }
    }

    public function render()
    {
        $sales = Sale::with(['customer', 'store', 'user'])
            ->where('store_id', auth()->user()->store_id)
            ->search($this->search)
            ->latest()
            ->paginate($this->perPage);


        return view('livewire.sale-table', [
            'sales' => $sales,
        ]);
    }
}
