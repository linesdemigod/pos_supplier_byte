<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AuditLog;
use App\Models\Supplier;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class SupplierManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;
    public function render()
    {
        $suppliers = Supplier::search($this->search)
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.supplier-manager', [
            'suppliers' => $suppliers,
        ]);
    }
}
