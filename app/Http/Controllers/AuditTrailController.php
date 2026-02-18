<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    function date()
    {


        return view('pages.audit.date');
    }

    public function get_date_audit(Request $request)
    {

        $validated = $request->validate([
            'date_from' => 'required',
            'date_to' => 'required',
        ]);

        $user = auth()->user();
        $warehouseId = $user->warehouse_id;
        $storeId = $user->store_id;

        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', 1);

        // Calculate the offset and limit
        $offset = ($page - 1) * $perPage;

        //add branch to the audit log
        $logs = AuditLog::with(['user', 'store', 'warehouse'])
            ->when($warehouseId !== null, function ($query) use ($warehouseId) {
                $query->where('warehouse_id', '=', $warehouseId);
            })
            ->when($storeId !== null, function ($query) use ($storeId) {
                $query->where('store_id', '=', $storeId);
            })
            ->whereDate('created_at', '>=', $validated['date_from'])
            ->whereDate('created_at', '<=', $validated['date_to'])
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);


        return response()->json([
            'logs' => $logs->items(),
            'current_page' => $logs->currentPage(),
            'last_page' => $logs->lastPage(),
            'total' => $logs->total(),
            'per_page' => $logs->perPage(),
        ], 200);

    }

    public function view_date_audit(Request $request)
    {

        $id = $request->id;

        $log = AuditLog::with(['user', 'store', 'warehouse'])
            ->where('id', '=', $id)
            ->first();

        return response()->json([
            'log' => $log
        ], 200);
    }

    public function user()
    {
        $user = auth()->user();
        $warehouseId = $user->warehouse_id;
        $storeId = $user->store_id;

        $users = User::when($warehouseId !== null, function ($query) use ($warehouseId) {
            $query->where('warehouse_id', '=', $warehouseId);
        })
            ->when($storeId !== null, function ($query) use ($storeId) {
                $query->where('store_id', '=', $storeId);
            })
            ->latest()
            ->get();


        return view('pages.audit.user', compact('users'));
    }

    function get_user_audit(Request $request)
    {

        $validated = $request->validate([
            'date_from' => 'required',
            'date_to' => 'required',
            'user' => 'exists:users,id'
        ]);

        $user = auth()->user();
        $warehouseId = $user->warehouse_id;
        $storeId = $user->store_id;

        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', 1);

        //add branch to the audit log
        $logs = AuditLog::with(['user', 'store', 'warehouse'])
            ->when($warehouseId !== null, function ($query) use ($warehouseId) {
                $query->where('warehouse_id', '=', $warehouseId);
            })
            ->when($storeId !== null, function ($query) use ($storeId) {
                $query->where('store_id', '=', $storeId);
            })
            ->where('user_id', $validated['user'])
            ->whereDate('created_at', '>=', $validated['date_from'])
            ->whereDate('created_at', '<=', $validated['date_to'])
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);


        return response()->json([
            'logs' => $logs->items(),
            'current_page' => $logs->currentPage(),
            'last_page' => $logs->lastPage(),
            'total' => $logs->total(),
            'per_page' => $logs->perPage(),
        ], 200);



    }

    public function view_user_audit(Request $request)
    {

        $id = $request->id;

        $log = AuditLog::with(['user', 'store', 'warehouse'])
            ->where('id', '=', $id)
            ->first();

        return response()->json([
            'log' => $log
        ], 200);
    }

    function user_audit()
    {

        return view('pages.audit.userlive');
    }
}
