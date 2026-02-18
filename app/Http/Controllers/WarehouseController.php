<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{

    public function index()
    {
        $warehouses = Warehouse::latest()
            ->paginate(10);

        return view('pages.warehouse.index', [
            'warehouses' => $warehouses,
        ]);
    }

    public function create()
    {

        return view('pages.warehouse.create');
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
        ], [
            'address.required' => 'The location field is required',
        ]);



        DB::beginTransaction();
        try {
            $data = Warehouse::create($validate);

            $auditTrail = [
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'description' => 'warehouse creation',
                'data_before' => json_encode([]), // no previous data since it's a new record
                'data_after' => json_encode($data->toArray()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();
            return back()->with('message', 'warehouse added successfully');
        } catch (\Exception $e) {
            Db::rollBack();
            return back()->with('error', 'Error adding warehouse');
        }

    }

    public function edit(Warehouse $warehouse)
    {



        return view('pages.warehouse.edit', [
            'warehouse' => $warehouse
        ]);
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validate = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
        ], [
            'address.required' => 'The location field is required',
        ]);

        // Get the original data before the update
        $originalData = $warehouse->getOriginal();

        DB::beginTransaction();
        try {

            $warehouse->update($validate);

            $auditTrail = [
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'description' => 'warehouse update',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($warehouse->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            return to_route('warehouse.index')->with('message', 'warehouse updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['an error while updating']);
        }

    }

    public function destroy(Request $request, Warehouse $warehouse)
    {

        $originalData = $warehouse->getOriginal();
        DB::beginTransaction();
        try {

            $warehouse->delete();

            $auditTrail = [
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'description' => 'warehouse deletion',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($warehouse->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();
            return back()->with('message', 'Warehouse deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'an error occured while deleting');
        }
    }
}
