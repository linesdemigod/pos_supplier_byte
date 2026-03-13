<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AuditLog;
use App\Models\Category;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class CategoryTable extends Component
{

    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';


    public function delete(Category $category)
    {

        // Get the original data before the update
        $originalData = $category->getOriginal();

        DB::beginTransaction();
        try {


            // delete it
            $category->delete();

            // Prepare audit trail data
            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => request()->ip(),
                'description' => 'category deletion',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            session()->flash('message', 'Category deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'An error ocurred while deleting ');
        }
    }

    public function render()
    {
        $categories = Category::
            search($this->search)
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.category-table', [
            'categories' => $categories,
        ]);
    }
}
