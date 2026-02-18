<?php

namespace App\Livewire;

use App\Models\SupplierPayment;
use App\Models\SupplierPurchase;
use DB;
use Livewire\Component;
use App\Models\AuditLog;
use App\Models\Supplier;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class SupplierPurchaseManager extends Component
{

    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['voidPurchase' => 'void'];

    public $supplierId;

    public function mount($supplierId)
    {
        $this->supplierId = $supplierId;
    }

    public function void(SupplierPurchase $purchase)
    {


        $user = auth()->user();
        $now = now();
        $originalData = $purchase->getOriginal();

        DB::beginTransaction();

        try {

            if (!$purchase) {
                return;
            }

            //change tenure status to voided
            $purchase->status = 'voided';
            $purchase->save();


            foreach ($purchase->supplierPurchaseItems as $orderItem) {

                //restore stock for each supplier order item
                $item = $orderItem->item;
                $item->quantity += $orderItem->quantity;
                $item->save();
            }

            foreach ($purchase->supplierPayments as $payment) {
                //restore stock for each supplier order item
                $payment->status = 'voided';
                $payment->save();
            }

            AuditLog::create([
                'user_id' => $user->id,
                'store_id' => $user->store_id,
                'ip_address' => request()->ip(),
                'description' => 'Purchase and payments voided',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($purchase->getAttributes()),
                'created_at' => $now,
                'updated_at' => $now,
            ]);


            DB::commit();
            session()->flash('message', 'Purchase voided successfully');
            $this->dispatch('formSubmitted', message: 'Purchase voided successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('message', 'An error occurred while voiding the purchase ' . $e->getMessage());
            $this->dispatch('formSubmitted', message: 'An error occurred while voiding the purchase ' . $e->getMessage());
        }


    }


    public function render()
    {
        $purchases = SupplierPurchase::where('supplier_id', $this->supplierId)
            ->search($this->search)
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.supplier-purchase-manager', [
            'purchases' => $purchases,
        ]);
    }
}
