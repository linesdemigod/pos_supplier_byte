<?php

namespace App\Livewire;

use App\Models\PriceHistory;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class ReportPriceHistory extends Component
{

    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {


        $items = PriceHistory::with(['item', 'user'])
            ->search($this->search)
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.report-price-history', [
            'items' => $items
        ]);
    }
}
