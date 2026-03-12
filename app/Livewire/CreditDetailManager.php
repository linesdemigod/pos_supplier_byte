<?php

namespace App\Livewire;

use Livewire\Component;

use DB;
use App\Models\Credit;
use App\Models\AuditLog;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class CreditDetailManager extends Component
{

    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['voidCredit' => 'void'];

    public $customerId;

    public function mount($customerId)
    {
        $this->customerId = $customerId;
    }

    public function void(Credit $credit)
    {


        $user = auth()->user();
        $now = now();
        $originalData = $credit->getOriginal();

        DB::beginTransaction();

        try {

            if (!$credit) {
                return;
            }

            //change tenure status to voided
            $credit->status = 'voided';
            $credit->save();


            foreach ($credit->creditOrderItems as $orderItem) {

                //restore stock for each credit order item
                $item = $orderItem->item;
                $item->quantity += $orderItem->quantity;
                $item->save();
            }

            foreach ($credit->repayments as $repayment) {
                //restore stock for each credit order item
                $repayment->payment_status = 'voided';
                $repayment->save();
            }

            AuditLog::create([
                'user_id' => $user->id,
                'tenant_id' => $user->tenant_id,
                'ip_address' => request()->ip(),
                'description' => 'Credit and repayments voided',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($credit->getAttributes()),
                'created_at' => $now,
                'updated_at' => $now,
            ]);


            DB::commit();
            session()->flash('message', 'Credit voided successfully');
            $this->dispatch('formSubmitted', message: 'Credit voided successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('message', 'An error occurred while voiding the credit ' . $e->getMessage());
            $this->dispatch('formSubmitted', message: 'An error occurred while voiding the credit ' . $e->getMessage());
        }


    }

    public function render()
    {

        $credits = Credit::with('user')
            ->where('customer_id', $this->customerId)
            ->date($this->search)
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.credit-detail-manager', [
            'credits' => $credits
        ]);
    }
}
