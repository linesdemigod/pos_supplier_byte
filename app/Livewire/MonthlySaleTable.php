<?php

namespace App\Livewire;

use App\Models\Sale;
use Livewire\Component;
use App\Models\AuditLog;
use App\Models\MonthlySale;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class MonthlySaleTable extends Component
{
    use WithPagination;

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';



    public function endMonthSale(MonthlySale $sale)
    {

        $user = auth()->user();
        $storeId = $user->store_id;
        $userId = $user->id;
        $today = now(); // Get today's date
        $currentMonth = $today->format('m');
        $currentYear = $today->format('Y');


        // get the sale
        $lastMonthlySale = MonthlySale::where('store_id', $storeId)
            ->where('id', $sale->id)
            ->first();

        $totalMonthSales = Sale::where('store_id', $storeId)
            ->where('monthly_sale_id', $lastMonthlySale->id)->sum('grandtotal');

        // Get the original data before the update
        $originalData = $sale->getOriginal();

        DB::beginTransaction();
        try {

            //close the sales
            $lastMonthlySale->status = 'closed';
            $lastMonthlySale->total_sales = $totalMonthSales ?? 0;
            $lastMonthlySale->close_date = now();
            $lastMonthlySale->save();


            // open new one
            $data = $this->createNewMonthlySale($storeId, $userId, $currentMonth, $currentYear);

            // Prepare audit trail data
            $auditTrail = [
                'user_id' => $userId,
                'store_id' => $storeId,
                'ip_address' => request()->ip(),
                'description' => 'Open monthly sale',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($data->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            session()->flash('message', 'Monthy Sale Opened successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'An error ocurred while Opening ');
        }
    }

    public function openMonthSale()
    {

        $user = auth()->user();
        $storeId = $user->store_id;
        $userId = $user->id;
        $today = now(); // Get today's date
        $currentMonth = $today->format('m');
        $currentYear = $today->format('Y');

        $lastMonthlySale = MonthlySale::where('store_id', $storeId)
            ->latest('id')
            ->first();

        if ($lastMonthlySale && $lastMonthlySale->status == 'open') {
            session()->flash('message', 'Monthly Sale is already Open');

            return;

        }

        DB::beginTransaction();
        try {



            $data = $this->createNewMonthlySale($storeId, $userId, $currentMonth, $currentYear);

            $auditTrail = [
                'user_id' => $userId,
                'store_id' => $storeId,
                'ip_address' => request()->ip(),
                'description' => 'Open monthly sale',
                'data_before' => json_encode([]),
                'data_after' => json_encode($data->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            session()->flash('message', 'Monthly Sale Opened successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'An error ocurred while Opening ');
        }
    }




    private function createNewMonthlySale($storeId, $userId, $currentMonth, $currentYear)
    {

        $data = MonthlySale::create([
            'user_id' => $userId,
            'store_id' => $storeId,
            'month' => $currentMonth,
            'year' => $currentYear,
            'open_date' => now(),
            'status' => 'open',
        ]);

        return $data;
    }


    public function render()
    {
        $sales = MonthlySale::with(['user', 'store'])
            ->where('store_id', auth()->user()->store_id)
            ->search($this->search)
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.monthly-sale-table', [
            'sales' => $sales,
        ]);
    }
}
