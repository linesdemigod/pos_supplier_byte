<?php

namespace App\Http\Controllers;


use App\Models\AuditLog;
use Illuminate\Http\Request;
use App\Models\StoreInventory;
use App\Models\QuantityHistory;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StoreInventoryImport;

class StoreInventoryController extends Controller
{

    public function index()
    {


        return view('pages.storeinventory.index');
    }

    public function create()
    {

        return view('pages.storeinventory.create');
    }

    public function store(Request $request)
    {

        $user = auth()->user();
        $storeId = $user->store_id;
        $userId = $user->id;

        $validate = $request->validate([
            'item_id' => 'required|exists:items,id|unique:store_inventories,item_id,NULL,id,store_id,' . $storeId,
            'quantity' => 'required|integer|min:0',
        ], [
            'item_id.required' => 'Item is required',
            'item_id.exists' => 'Item does not exist',
            'item_id.unique' => 'Item already exists in the store',
        ]);

        //send error message when store id is null
        if ($storeId === null) {
            return back()->with('error', 'please switch to a store to add an item.');
        }


        DB::beginTransaction();
        try {
            $validate['store_id'] = $storeId;

            $data = StoreInventory::create($validate);

            QuantityHistory::create([
                'user_id' => $userId,
                'store_id' => $storeId,
                'item_id' => $data->item_id,
                'old_quantity' => 0,
                'new_quantity' => $data->quantity,
                'change_type' => 'Add',
            ]);


            $auditTrail = [
                'user_id' => $userId,
                'store_id' => $storeId,
                'ip_address' => $request->ip(),
                'description' => 'store inventory creation',
                'data_before' => json_encode([]), // no previous data since it's a new record
                'data_after' => json_encode($data->toArray()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();
            return back()->with('message', 'Item added to the store successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error adding Item ro the store ');
        }

    }

    public function edit(StoreInventory $inventory)
    {

        $user = auth()->user();
        $storeId = $user->store_id;

        //send error message when store id is null
        if ($storeId === null) {
            return back()->with('error', 'please switch to a store to add an item.');
        }

        return view('pages.storeinventory.edit', [
            'inventory' => $inventory->load('item')
        ]);
    }

    public function update(Request $request, StoreInventory $inventory)
    {
        $validate = $request->validate([
            'quantity' => 'required|integer|min:1',
            'stock' => 'required',
        ]);

        // Get the original data before the update
        $originalData = $inventory->getOriginal();
        $user = auth()->user();
        $storeId = $user->store_id;
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
                'store_id' => $storeId,
                'ip_address' => $request->ip(),
                'description' => 'store inventory update',
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
                'store_id' => $storeId,
                'item_id' => $inventory->item_id,
                'old_quantity' => $inventory->quantity,
                'new_quantity' => $validate['quantity'],
                'change_type' => $validate['stock'],
            ]);

            DB::commit();

            return to_route('storeinventory.index')->with('message', 'store inventory updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['an error while updating quantity']);
        }

    }

    public function destroy(Request $request, StoreInventory $inventory)
    {


        $originalData = $inventory->getOriginal();


        DB::beginTransaction();
        try {

            $inventory->delete();

            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => $request->ip(),
                'description' => 'store inventory deletion',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($inventory->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();
            return back()->with('message', 'Store inventory deleted successfully');
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
            Excel::import(new StoreInventoryImport, $request->file('excel'));

            return response()->json(['success' => 'Items added to store successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (\Throwable $e) {
            // do what you want to do on exception catching 
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
}
