<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class CustomerCreditManager extends Component
{
    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $customersWithCredits = Customer::whereHas('credits', function ($query) {
            $query->whereIn('status', ['credit', 'partial', 'paid']);
        })
            ->withSum([
                'credits as total_credit_amount' => function ($query) {
                    $query->whereIn('status', ['credit', 'partial', 'paid']);
                }
            ], 'total_amount')
            ->addSelect([
                'total_repaid_amount' => DB::table('repayments')
                    ->selectRaw('COALESCE(SUM(repayments.amount_paid), 0)')
                    ->join('credits', 'credits.id', '=', 'repayments.credit_id')
                    ->whereIn('credits.status', ['credit', 'partial', 'paid'])
                    ->where('repayments.payment_status', 'paid')
                    ->where('repayments.store_id', auth()->user()->store_id)
                    ->whereColumn('credits.customer_id', 'customers.id'),
            ])
            ->search($this->search)
            ->latest()
            ->paginate($this->perPage);

        // Compute outstanding balance
        $customersWithCredits->getCollection()->transform(function ($customer) {
            $customer->outstanding = ($customer->total_credit_amount ?? 0) - ($customer->total_repaid_amount ?? 0);
            return $customer;
        });


        return view('livewire.customer-credit-manager', [
            'customers' => $customersWithCredits,
        ]);
    }
}
