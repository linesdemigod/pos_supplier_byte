<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\AuditLog;
use App\Models\Category;
use App\Imports\ItemImport;
use App\Models\PriceHistory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{
    public function index()
    {

        return view('pages.item.index');
    }

    public function create()
    {

        $categories = Category::latest()
            ->get();

        return view('pages.item.create', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required',
            'price' => ['required', 'regex:/^\d+(\.\d+)?$/'],
            'item_code' => ['required', Rule::unique('items', 'item_code')],
        ]);

        DB::beginTransaction();
        try {
            $data = Item::create($validate);

            $auditTrail = [
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'store_id' => auth()->user()->store_id,
                'description' => 'item creation',
                'data_before' => json_encode([]), // no previous data since it's a new record
                'data_after' => json_encode($data->toArray()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();
            return back()->with('message', 'item added successfully');
        } catch (\Exception $e) {
            Db::rollBack();
            return back()->with('error', 'Error adding item');
        }

    }

    public function edit(Item $item)
    {

        $categories = Category::latest()
            ->get();

        return view('pages.item.edit', [
            'item' => $item,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, Item $item)
    {
        $validate = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required',
            'price' => ['required', 'regex:/^\d+(\.\d+)?$/'],
            'item_code' => ['required', Rule::unique('items', 'item_code')->ignore($item->id)],
        ]);

        // Get the original data before the update
        $originalData = $item->getOriginal();

        DB::beginTransaction();
        try {

            $item->update($validate);

            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => $request->ip(),
                'description' => 'item update',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($item->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            return to_route('item.index')->with('message', 'item updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['an error while updating']);
        }

    }

    public function getItem(Request $request)
    {
        $searchQuery = $request->input('item');


        $items = Item::filter($searchQuery)
            ->latest() // Order by latest
            ->get();

        return response()->json(
            [
                'items' => $items,
            ],
            200
        );
    }

    public function getWarehouseItem(Request $request)
    {

        $request->validate([
            'item' => 'required|string',
            'warehouse_id' => 'required|exists:warehouses,id',
        ], [
            'item.required' => 'Item search query is required',
            'item.string' => 'Item search query must be a string',
            'warehouse_id.required' => 'Select a warehouse is required',
            'warehouse_id.exists' => 'Warehouse does not exist',
        ]);

        $searchQuery = $request->input('item');
        $warehouseId = $request->input('warehouse_id');

        //search item in the specified warehouse
        $items = Item::whereHas('warehouseInventories', function ($query) use ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        })
            ->filter($searchQuery)
            ->latest() // Order by latest
            ->get();

        // $items = Item::filter($searchQuery)
        //     ->latest() // Order by latest
        //     ->get();

        return response()->json(
            [
                'items' => $items,
            ],
            200
        );
    }

    public function excelImport(Request $request)
    {

        $request->validate([
            'excel' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new ItemImport, $request->file('excel'));

            return response()->json(['success' => 'Items added successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'an error occurred while importing'], 500);
        }

    }

    public function priceAdjustment()
    {

        return view('pages.item.price');
    }

    public function priceAdjustmentStore(Request $request)
    {
        $validate = $request->validate([
            'id' => 'required|exists:items,id',
            'price' => ['required', 'regex:/^\d+(\.\d+)?$/'],
        ], [
            'price.regex' => 'Price should be a number',
            'id.required' => 'Item is required',
            'id.exists' => 'Item does not exist',
        ]);

        $item = Item::findOrFail($validate['id']);

        $originalData = $item->getOriginal();

        DB::beginTransaction();

        try {

            $item->update($validate);

            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => $request->ip(),
                'description' => 'item update',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($item->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            //update price history
            PriceHistory::create([
                'user_id' => auth()->id(),
                'item_id' => $item->id,
                'new_price' => $item->price,
                'old_price' => $originalData['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return to_route('item.price.adjustment')->with('message', 'item price updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['an error while updating']);
        }

    }
}
