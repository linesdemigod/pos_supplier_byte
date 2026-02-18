<?php

namespace App\Http\Controllers;

use App\Models\DailySale;
use App\Models\Sale;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class DailySaleController extends Controller
{

    public function index()
    {

        return view('pages.dailysale.index');
    }

    public function setDailySale()
    {
        $user = auth()->user();
        $store = $user->store_id;
        $userId = $user->id;
        $todayDate = date('Y-m-d');

        // Get the last daily sale
        $lastDailySale = DailySale::where('store_id', $store)->latest('id')->first();

        if ($lastDailySale) {
            //calculate the sales for that day
            $totalSalesForDay = Sale::where('store_id', $store)
                ->where('daily_sale_id', $lastDailySale->id)->sum('grandtotal');

            if (Str::lower($lastDailySale->status) === 'closed') {
                // Open new daily sale
                $this->createNewDailySale($store, $userId, $todayDate);
            } elseif ($lastDailySale->date < $todayDate) {
                // Close the old daily sale and open a new one
                $lastDailySale->status = 'closed';
                $lastDailySale->total_sales = $totalSalesForDay;
                $lastDailySale->close_time = now();
                $lastDailySale->save();
                $this->createNewDailySale($store, $userId, $todayDate);
            }
        } else {
            // No previous daily sale, create a new one
            $this->createNewDailySale($store, $userId, $todayDate);
        }
    }

    private function createNewDailySale($store, $userId, $todayDate)
    {
        DailySale::create([
            'store_id' => $store,
            'status' => 'open',
            'date' => $todayDate,
            'user_id' => $userId,
            'open_time' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

}
