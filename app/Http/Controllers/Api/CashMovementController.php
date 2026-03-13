<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CashMovement;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashMovementController extends Controller
{
    public function index(Request $request)
    {

        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', 1);
        $search = $request->input('q');

        $records = CashMovement::with(['approvedBy', 'user'])
            ->search($search)
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);


        return response()->json([
            'records' => $records->items(),
            'current_page' => $records->currentPage(),
            'last_page' => $records->lastPage(),
            'total' => $records->total(),
            'per_page' => $records->perPage(),
        ], 200);
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
            return response()->json(['message' => 'Cash movement created successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create cash movement.'], 500);
        }
    }

    private function userShift()
    {

        $userId = auth()->id();

        $shift = Shift::where('user_id', $userId)->latest('id')->value('id');

        return $shift;

    }

}
