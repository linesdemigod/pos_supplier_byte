<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    public function index()
    {

        $userId = auth()->id();

        $shift = Shift::where('user_id', $userId)->latest('id')->first();

        $shiftTotal = null;

        if ($shift) {
            $shiftTotal = $this->getUserShiftOrdersTotal($shift->id);
        }

        return view('pages.shift.index', compact('shift', 'shiftTotal'));
    }

    public function openShift()
    {

        $userId = auth()->id();
        $existingShift = Shift::where('user_id', $userId)->whereNull('end_time')->first();

        if ($existingShift) {
            return back()->with('error', 'You already have an open shift.');
        }


        Shift::create([
            'user_id' => $userId,
            'start_time' => now(),
            'end_time' => null,
            'total_sales' => null
        ]);

        return back()->with('message', 'Shift opened successfully');
    }

    public function closeShift(Shift $shift)
    {
        //protect shift
        // Gate::authorize('update', $shift);

        //disallow closing of shift if user's shift orders are not paid
        $unpaidOrdersCount = Sale::where('shift_id', $shift->id)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->count();

        if ($unpaidOrdersCount > 0) {
            return back()->with('error', 'Cannot close shift with unpaid orders.');
        }


        $shiftTotal = Sale::where('shift_id', $shift->id)
            ->where('status', 'active')
            ->sum('amount_paid');


        $updateShift = $shift->update([
            'end_time' => now(),
            'total_sales' => $shiftTotal ?? 0,
            'status' => 'closed'
        ]);

        return back()->with('message', 'Shift closed successfully');
    }


    private function getUserShiftOrdersTotal($id)
    {
        $shiftTotal = Sale::select([
            DB::raw('COALESCE(SUM(subtotal), 0) AS subtotal'),
            DB::raw('COALESCE(SUM(grandtotal), 0) AS total'),
            DB::raw('COALESCE(SUM(discount), 0) AS discount'),
        ])->whereIn('payment_status', ['paid', 'partial'])
            ->where('shift_id', $id)
            ->first();

        return $shiftTotal;
    }
}
