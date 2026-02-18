<?php

namespace App\Livewire;

use App\Models\Sale;

use Livewire\Component;
use App\Models\Customer;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ReportCustomerPurchase extends Component
{

    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';


    public function render()
    {

        $storeId = auth()->user()->store_id;

        $customers = Sale::select('customers.name', 'customers.id', DB::raw('SUM(sales.grandtotal) AS grandtotal'), DB::raw('COUNT(sales.customer_id) AS visits'))
            ->where('payment_status', 'paid')
            ->join('customers', 'customers.id', 'sales.customer_id')
            ->customerSearch($this->search)
            ->groupBy('customers.id', 'customers.name')
            ->paginate($this->perPage);

        return view('livewire.report-customer-purchase', [
            'customers' => $customers,
        ]);
    }
}
