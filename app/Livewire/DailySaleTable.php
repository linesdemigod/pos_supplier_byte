<?php

namespace App\Livewire;

use App\Models\Sale;
use Livewire\Component;
use App\Models\AuditLog;
use App\Models\DailySale;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class DailySaleTable extends Component
{

    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';


    public function endDaySale(DailySale $sale)
    {

        $user = auth()->user();
        $storeId = $user->store_id;
        $userId = $user->id;
        $todayDate = date('Y-m-d');

        // get the sale
        $lastDailySale = DailySale::where('store_id', $storeId)
            ->where('id', $sale->id)
            ->first();

        $totalSalesForDay = Sale::where('store_id', $storeId)
            ->where('daily_sale_id', $lastDailySale->id)->sum('grandtotal');

        // Get the original data before the update
        $originalData = $sale->getOriginal();

        DB::beginTransaction();
        try {

            //close the sales
            $lastDailySale->status = 'closed';
            $lastDailySale->total_sales = $totalSalesForDay;
            $lastDailySale->close_time = now();
            $lastDailySale->save();


            // open new one
            $data = $this->createNewDailySale($storeId, $userId, $todayDate);

            // Prepare audit trail data
            $auditTrail = [
                'user_id' => $userId,
                'store_id' => $storeId,
                'ip_address' => request()->ip(),
                'description' => 'Open daily sale',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($data->toArray()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            session()->flash('message', 'Daily Sale Opened successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'An error ocurred while Opening ');
        }
    }


    private function createNewDailySale($store, $userId, $todayDate)
    {
        $data = DailySale::create([
            'store_id' => $store,
            'status' => 'open',
            'date' => $todayDate,
            'user_id' => $userId,
            'open_time' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $data;
    }

    public function render()
    {


        $sales = DailySale::with(['user', 'store'])
            ->where('store_id', auth()->user()->store_id)
            ->search($this->search)
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.daily-sale-table', [
            'sales' => $sales
        ]);
    }
}
