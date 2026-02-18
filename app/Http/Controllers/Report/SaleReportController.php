<?php

namespace App\Http\Controllers\Report;

use App\Models\Sale;
use App\Models\ReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SaleReportController extends Controller
{
    public function summary()
    {
        return view('pages.report.sales.details');
    }

    public function get_summary(Request $request)
    {
        $request->validate([
            'from' => 'required',
            'to' => 'required',
        ]);


        $orders = Sale::select('sales.*', 'users.name AS staff')
            ->join('users', 'users.id', 'sales.user_id')
            ->where('sales.store_id', auth()->user()->store_id)
            ->where('sales.payment_status', '=', 'paid');




        $records = $orders->whereDate('sales.created_at', '>=', $request->from)
            ->whereDate('sales.created_at', '<=', $request->to)
            ->orderBy('sales.created_at', 'DESC')
            ->get();

        return response()->json($records);
    }



    public function analytics()
    {

        return view('pages.report.sales.summary');
    }

    public function salesTrends()
    {

        return view('pages.report.sales.sales-trends');
    }

    public function itemSales()
    {

        return view('pages.report.sales.item-sales');
    }



    public function get_analytics(Request $request)
    {
        $period = $request->period;


        $dates = $this->getDates($period);

        $orders = Sale::select(DB::raw('COALESCE(SUM(subtotal), 0) AS subtotal'), DB::raw('COALESCE(SUM(grandtotal), 0) AS grandtotal'), DB::raw('COALESCE(SUM(discount), 0) AS discount'))
            ->where('store_id', auth()->user()->store_id)
            ->where('payment_status', '=', 'paid');

        $perHourSales = Sale::where('store_id', auth()->user()->store_id)
            ->where('payment_status', '=', 'paid');

        //fetch return item within the period
        $returnItemsTotal = ReturnItem::select(DB::raw('COALESCE(SUM(total), 0) AS return_total'))
            ->where('store_id', auth()->user()->store_id);



        if (in_array($period, ['today', 'yesterday'])) {
            $orders->whereDate('created_at', $dates[0]);
            $perHourSales->whereDate('sales.created_at', $dates[0]);

            $returnItemsTotal->whereDate('return_date', $dates[0]);

        } else {
            $orders->whereBetween('created_at', $dates);

            $perHourSales->whereBetween('sales.created_at', $dates);

            $returnItemsTotal->whereBetween('return_date', $dates);
        }



        $records = $orders->first();

        $hourSales = $perHourSales->select(
            DB::raw('DATE_FORMAT(created_at, "%h:00 %p") as hour'),
            DB::raw('COALESCE(SUM(grandtotal), 0) as grandtotal')
        )
            ->groupBy('hour')
            // ->orderBy('hour', 'desc')
            ->get();

        $hours = collect(range(0, 23))->map(function ($h) {
            return Carbon::createFromTime($h)->format('h:00 A');
        });

        $finalPerHour = $hours->map(function ($hour) use ($hourSales) {
            $match = $hourSales->firstWhere('hour', $hour);
            return [
                'hour' => $hour,
                'grandtotal' => $match ? $match->grandtotal : 0
            ];
        });


        $itemReturnedSum = $returnItemsTotal->first();


        return response()->json([
            'record' => $records,
            'hourSales' => $finalPerHour,
            'itemReturnedSum' => $itemReturnedSum,
        ]);
    }

    private function getDates($period)
    {
        switch ($period) {
            case 'today':
                return [now()->toDateString()];
            case 'yesterday':
                return [now()->subDay()->toDateString()];
            case 'weekly':
                return [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()];
            case 'monthly':
                return [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()];
            case 'yearly':
                return [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()];
            default:
                return [];
        }
    }
}
