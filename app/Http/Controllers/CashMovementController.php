<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\CashMovement;
use App\Models\Shift;
use DB;
use Illuminate\Http\Request;

class CashMovementController extends Controller
{
    public function index()
    {
        return view('pages.cashmovement.index');
    }

    public function create()
    {
        return view('pages.cashmovement.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'amount' => ['required', 'regex:/^\d+(\.\d+)?$/'],
            'type' => 'required|in:in,out',
            'reason' => 'nullable|string',
        ]);

        $user = auth()->user();
        $userId = $user->id;
        $warehouseId = $user->warehouse_id;
        $storeId = $user->store_id;

        DB::beginTransaction();
        try {
            // Create the cash movement record
            $cashMovement = CashMovement::create([
                'amount' => $validatedData['amount'],
                'type' => $validatedData['type'],
                'reason' => $validatedData['reason'] ?? null,
                'shift_id' => $this->userShift(),
                'user_id' => $userId,
            ]);

            $auditTrail = [
                'user_id' => $userId,
                'ip_address' => $request->ip(),
                'store_id' => $storeId,
                'warehouse_id' => $warehouseId,
                'description' => 'Cash movement creation',
                'data_before' => json_encode([]), // no previous data since it's a new record
                'data_after' => json_encode($cashMovement->toArray()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Log the creation in the audit log
            AuditLog::create($auditTrail);

            DB::commit();
            return back()->with('success', 'Cash movement created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while creating the cash movement. ' . $e->getMessage());
        }
    }

    private function userShift()
    {

        $userId = auth()->id();

        $shift = Shift::where('user_id', $userId)->latest('id')->value('id');

        return $shift;

    }

    public function edit(CashMovement $record)
    {
        return view('pages.cashmovement.edit', ['record' => $record]);
    }

    public function update(Request $request, CashMovement $record)
    {
        $validatedData = $request->validate([
            'amount' => ['required', 'regex:/^\d+(\.\d+)?$/'],
            'type' => 'required|in:in,out',
            'reason' => 'nullable|string',
        ]);


        // Get the original data before the update
        $originalData = $record->getOriginal();

        DB::beginTransaction();
        try {


            // delete it
            $record->update(
                [
                    'amount' => $validatedData['amount'],
                    'type' => $validatedData['type'],
                    'reason' => $validatedData['reason'] ?? null,
                    'updated_at' => now(),
                ]
            );

            // Prepare audit trail data
            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => request()->ip(),
                'description' => 'cash Movement update',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();
            return to_route('cash_movement.index')->with('message', 'Cash movement updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('cash_movement.index')->with('error', 'An error ocurred while updating the cash movement. ' . $e->getMessage());
        }
    }


}
