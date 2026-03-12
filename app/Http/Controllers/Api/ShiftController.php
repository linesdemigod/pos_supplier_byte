<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Shift;
use Carbon\Carbon;
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

        $forceCloseShift = false;

        if ($shift && $shift->status === 'open') {
            $shiftDate = Carbon::parse($shift->opened_at)->toDateString();
            $today = Carbon::today()->toDateString();

            if ($shiftDate < $today) {
                $forceCloseShift = true;
            }
        }

        return response()->json([
            'shift' => $shift,
            'shiftTotal' => $shiftTotal,
            'forceCloseShift' => $forceCloseShift,
        ], 200);
    }

    public function openShift(Request $request)
    {
        $validated = $request->validate([
            'starting_cash' => ['required', 'regex:/^\d+(\.\d+)?$/'],
        ]);

        $userId = auth()->id();
        $existingShift = Shift::where('user_id', $userId)->whereNull('closed_at')->first();

        if ($existingShift) {
            return response()->json(['error' => 'You already have an open shift.'], 400);
        }


        Shift::create([
            'user_id' => $userId,
            'opened_at' => now(),
            'starting_cash' => $validated['starting_cash'],
        ]);

        return response()->json(['message' => 'Shift opened successfully'], 200);
    }

    public function closeShift(Request $request, $id)
    {

        $validated = $request->validate([
            'closing_cash' => ['required', 'regex:/^\d+(\.\d+)?$/'],
        ]);

        $shift = Shift::findOrFail($id);

        //protect shift
        // Gate::authorize('update', $shift);

        //disallow closing of shift if user's shift orders are not paid
        $unpaidOrdersCount = Sale::where('shift_id', $shift->id)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->count();

        if ($unpaidOrdersCount > 0) {
            return response()->json(['message' => 'Cannot close shift with unpaid orders.'], 400);
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

        return response()->json(['message' => 'Shift closed successfully'], 200);
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
