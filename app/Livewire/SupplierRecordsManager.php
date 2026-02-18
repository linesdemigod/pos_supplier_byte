<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Supplier;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class SupplierRecordsManager extends Component
{
    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $suppliers = Supplier::search($this->search)
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.supplier-records-manager', [
            'suppliers' => $suppliers
        ]);
    }
}
