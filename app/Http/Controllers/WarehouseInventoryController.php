<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use App\Models\QuantityHistory;
use App\Models\WarehouseInventory;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\WarehouseInventoryImport;

class WarehouseInventoryController extends Controller
{

    public function index()
    {

        return view('pages.warehouseinventory.index');
    }

    public function create()
    {

        return view('pages.warehouseinventory.create');
    }

    public function store(Request $request)
    {

        $user = auth()->user();
        $warehouseId = $user->warehouse_id;
        $userId = $user->id;



        $validate = $request->validate([
            'item_id' => 'required|exists:items,id|unique:warehouse_inventories,item_id,NULL,id,warehouse_id,' . $warehouseId,
            'quantity' => 'required|integer|min:0',
        ], [
            'item_id.required' => 'Item is required',
            'item_id.exists' => 'Item does not exist',
            'item_id.unique' => 'Item already exists in the warehouse',
        ]);

        //send error message when warehouse id is null
        if ($warehouseId === null) {
            return back()->with('error', 'please switch to a warehouse to add an item.');
        }

        DB::beginTransaction();
        try {


            $validate['warehouse_id'] = $warehouseId;

            $data = WarehouseInventory::create($validate);

            QuantityHistory::create([
                'user_id' => $userId,
                'warehouse_id' => $warehouseId,
                'item_id' => $data->item_id,
                'old_quantity' => 0,
                'new_quantity' => $data->quantity,
                'change_type' => 'Add',
            ]);

            $auditTrail = [
                'user_id' => $userId,
                'warehouse_id' => $warehouseId,
                'ip_address' => $request->ip(),
                'description' => 'warehouse inventory creation',
                'data_before' => json_encode([]), // no previous data since it's a new record
                'data_after' => json_encode($data->toArray()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();
            return back()->with('message', 'Item added to the warehouse successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error adding Item to the warehouse ');
        }

    }

    public function edit(WarehouseInventory $inventory)
    {
        $user = auth()->user();
        $warehouseId = $user->warehouse_id;

        //send error message when warehouse id is null
        if ($warehouseId === null) {
            return back()->with('error', 'please switch to a warehouse to add an item.');
        }



        return view('pages.warehouseinventory.edit', [
            'inventory' => $inventory->load('item')
        ]);
    }

    public function update(Request $request, WarehouseInventory $inventory)
    {
        $validate = $request->validate([
            'quantity' => 'required|integer|min:1',
            'stock' => 'required',
        ]);

        // Get the original data before the update
        $originalData = $inventory->getOriginal();
        $user = auth()->user();
        $warehouseId = $user->warehouse_id;
        $userId = $user->id;

        DB::beginTransaction();
        try {



            switch ($validate['stock']) {
                case 'Add':
                    $inventory->quantity += $validate['quantity'];
                    $inventory->save();
                    break;
                case 'Minus':
                    $inventory->quantity -= $validate['quantity'];
                    $inventory->save();
                    break;
                default:
                    break;
            }

            // $inventory->update($validate);

            $auditTrail = [
                'user_id' => $userId,
                'warehouse_id' => $warehouseId,
                'ip_address' => $request->ip(),
                'description' => 'warehouse inventory update',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($inventory->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            //quantity history
            QuantityHistory::create([
                'user_id' => $userId,
                'warehouse_id' => $warehouseId,
                'item_id' => $inventory->item_id,
                'old_quantity' => $inventory->quantity,
                'new_quantity' => $validate['quantity'],
                'change_type' => $validate['stock'],
            ]);

            DB::commit();

            return to_route('warehouseinventory.index')->with('message', 'warehouse inventory updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['an error while updating quantity']);
        }

    }

    public function destroy(Request $request, WarehouseInventory $inventory)
    {

        $originalData = $inventory->getOriginal();
        DB::beginTransaction();
        try {

            $inventory->delete();

            $auditTrail = [
                'user_id' => auth()->id(),
                'warehouse_id' => auth()->user()->warehouse_id,
                'ip_address' => $request->ip(),
                'description' => 'warehouse inventory deletion',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($inventory->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();
            return back()->with('message', 'Warehouse inventory deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'an error occured while deleting');
        }

    }

    public function excelImport(Request $request)
    {

        $request->validate([
            'excel' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new WarehouseInventoryImport, $request->file('excel'));

            return response()->json(['success' => 'Items added to warehouse successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['an error occurred while importing' => $e->getMessage()], 500);
        }

    }
}
