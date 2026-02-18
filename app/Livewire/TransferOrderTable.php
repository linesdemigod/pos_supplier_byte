<?php

namespace App\Livewire;

use App\Models\TransferOrder;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class TransferOrderTable extends Component
{
    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    public $status = 'all';

    protected $paginationTheme = 'bootstrap';
    public function render()
    {
        $user = auth()->user();
        $filterColumn = $user->store_id ? 'store_id' : 'warehouse_id';
        $filterValue = $user->store_id ?: $user->warehouse_id;

        $items = TransferOrder::with(['user', 'store', 'warehouse', 'storeRequest'])
            ->where($filterColumn, $filterValue)
            ->when($this->status !== 'all', function ($query) {
                $query->where('status', $this->status);
            })
            ->search($this->search)
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.transfer-order-table', [
            'items' => $items,
        ]);
    }
}
