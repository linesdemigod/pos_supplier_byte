<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Store;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\StoreInventory;
use App\Models\WarehouseInventory;

class DashboardController extends Controller
{
    public function index()
    {


        return view('pages.home.index');
    }

    public function stockLevelNotification()
    {
        $user = auth()->user();
        $lowStockMessage = 'Some items are running out of stock';

        // Check for low stock in the store
        if (StoreInventory::where('store_id', $user->store_id)->where('quantity', '<=', 10)->exists()) {
            return response()->json(['message' => $lowStockMessage], 200);
        }

        // Check for low stock in the warehouse
        if ($user->warehouse_id && WarehouseInventory::where('warehouse_id', $user->warehouse_id)->where('quantity', '<=', 10)->exists()) {
            return response()->json(['message' => $lowStockMessage], 200);
        }

        return response()->json(['message' => ''], 200);
    }


    public function store()
    {
        $stores = Store::latest()->get();
        $warehouses = Warehouse::latest()->get();

        return view('pages.home.store', compact('stores', 'warehouses'));
    }

    public function selectStore(Request $request)
    {
        $validated = $request->validate([
            'id' => 'numeric|exists:stores,id',
        ]);

        $store = Store::find($request->id);


        if (!$store) {
            return back()->with('message', 'Store does not exist');
        } else {
            //update the admin store
            $user = User::where('id', auth()->id())->update(['store_id' => $validated['id'], 'warehouse_id' => null]);
            return redirect()->route('dashboard')->with('message', 'Welcome back ');
        }

    }

    public function selectWarehouse(Request $request)
    {
        $validated = $request->validate([
            'id' => 'numeric|exists:warehouses,id',
        ]);


        $warehouse = Warehouse::find($request->id);

        if (!$warehouse) {
            return back()->with('message', 'warehouse does not exist');
        } else {
            //update the admin store
            $user = User::where('id', auth()->id())->update(['warehouse_id' => $validated['id'], 'store_id' => null]);
            return redirect()->route('dashboard')->with('message', 'Welcome back ');
        }

    }
}
