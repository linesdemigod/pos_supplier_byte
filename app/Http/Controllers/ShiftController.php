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

    public function openShift(Request $request)
    {
        $validated = $request->validate([
            'opening_cash' => ['required', 'regex:/^\d+(\.\d+)?$/'],
        ]);

        $userId = auth()->id();
        $existingShift = Shift::where('user_id', $userId)->whereNull('closed_at')->first();

        if ($existingShift) {
            return back()->with('error', 'You already have an open shift.');
        }


        Shift::create([
            'user_id' => $userId,
            'opened_at' => now(),
            'opening_cash' => $validated['opening_cash'],
        ]);

        return back()->with('message', 'Shift opened successfully');
    }

    public function closeShift(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'closing_cash' => ['required', 'regex:/^\d+(\.\d+)?$/'],
        ]);

        //disallow closing of shift if user's shift orders are not paid
        $unpaidOrdersCount = Sale::where('shift_id', $shift->id)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->count();

        if ($unpaidOrdersCount > 0) {
            return back()->with('error', 'Cannot close shift with unpaid orders.');
        }


        $shiftTotal = Sale::where('shift_id', $shift->id)
            ->where('payment_status', 'paid')
            ->sum('grandtotal');


        //calculate cash difference
        $cashDifference = ($shift->opening_cash + $shiftTotal) - $validated['closing_cash'];


        $updateShift = $shift->update([
            'closed_at' => now(),
            'expected_cash' => $shiftTotal ?? 0,
            'status' => 'closed',
            'closing_cash' => $validated['closing_cash'],
            'cash_difference' => $cashDifference,
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
