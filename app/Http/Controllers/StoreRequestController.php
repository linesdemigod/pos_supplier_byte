<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\AuditLog;
use App\Models\Warehouse;
use App\Models\StoreRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class StoreRequestController extends Controller
{
    public function index()
    {


        return view('pages.storerequest.index');
    }

    public function create()
    {
        $storeId = auth()->user()->store_id;
        // Gate::authorize('allowed', $storeId);
        if (!$storeId) {
            abort(403, 'Forbidden: Access denied');
        }

        $warehouses = Warehouse::latest()
            ->get();

        return view('pages.storerequest.create', [
            'warehouses' => $warehouses,
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'reference' => ['required', 'numeric', Rule::unique('item_requests', 'reference')],
            'warehouse' => 'required|exists:warehouses,id',
        ]);

        $storeId = auth()->user()->store_id;



        $saleItems = $request->input('items');
        $reference = $request->input('reference');
        $user = auth()->user();
        $userId = $user->id;
        $storeId = $user->store_id;
        $warehouseId = $request->input('warehouse');


        DB::beginTransaction();
        try {


            $requestedData = [
                'requested_by' => $userId,
                'reference' => $reference,
                'store_id' => $storeId,
                'warehouse_id' => $warehouseId,
                'status' => 'pending',
                'requested_date' => now()
            ];

            $requestItemsData = [];
            //loop through the itens and prepare the sales data
            foreach ($saleItems as $item) {


                $requestItemsData[] = [

                    'item_id' => $item['id'],
                    'requested_quantity' => $item['quantity'],
                ];


            }


            $storerequest = StoreRequest::create($requestedData); //insert into storerequests
            foreach ($requestItemsData as $item) {
                $item['store_request_id'] = $storerequest->id;
            }
            $storerequest->storerequestDetails()->createMany($requestItemsData); //insert into sale items

            //audit trail
            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => $request->ip(),
                'description' => 'store request creation',
                'data_before' => json_encode([]),
                'data_after' => json_encode($storerequest->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            // commit the transaction
            DB::commit();

            // return a response indicating the order was successfully processed
            return response()->json(['message' => 'store request processed successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error while processing request ' . $e->getMessage()], 500);
        }


    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'warehouse' => 'required|exists:warehouses,id',
            'requestId' => 'required|exists:store_requests,id'
        ]);

        // Extract required inputs
        $requestedItems = $validated['items'];
        $warehouseId = $validated['warehouse'];
        $requestId = $validated['requestId'];
        $userId = auth()->id();

        // Fetch the existing request
        $storerequest = StoreRequest::findOrFail($requestId);

        $originalData = $storerequest->getOriginal();

        DB::beginTransaction();

        try {

            $total = 0;

            // Prepare data for the request update
            $requestItemsData = array_map(function ($item) use (&$total) {

                return [
                    'item_id' => $item['id'],
                    'requested_quantity' => $item['quantity'],

                ];
            }, $requestedItems);

            $requestedData = [
                'requested_by' => $userId,
                'warehouse_id' => $warehouseId,
            ];

            // Update the item request
            $storerequest->update($requestedData);

            // Clear old request details and insert the new ones
            $storerequest->storeRequestDetails()->delete();
            $storerequest->storeRequestDetails()->createMany($requestItemsData);

            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => $request->ip(),
                'description' => 'store request update',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($storerequest->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            return response()->json(['message' => 'Items request updated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error while processing request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function edit(StoreRequest $item)
    {


        Gate::authorize('edit', $item); //allow only those who belong to that store to edit

        $warehouses = Warehouse::latest()
            ->get();

        // dd($item->total);
        $items = $item->load('storeRequestDetails.item');


        return view('pages.storerequest.edit', [
            'warehouses' => $warehouses,
            'item' => $item,
            'storeItems' => $items,
        ]);
    }

    public function show(Storerequest $item)
    {

        //prevent unauthorized access
        Gate::authorize('show', $item);


        return view('pages.storerequest.show', [
            'requestItems' => $item->load(['storeRequestDetails.item', 'store', 'warehouse', 'approvedBy', 'requestedBy'])
        ]);
    }
}
