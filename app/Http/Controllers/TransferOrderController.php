<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Company;
use App\Models\AuditLog;
use Illuminate\Support\Str;
use App\Models\StoreRequest;
use Illuminate\Http\Request;
use App\Models\TransferOrder;
use App\Models\WarehouseInventory;
use Illuminate\Support\Facades\DB;
use App\Models\TransferOrderDetail;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class TransferOrderController extends Controller
{

    public function index()
    {

        return view('pages.transfer.index');
    }

    public function show(TransferOrder $transfer)
    {

        Gate::authorize('show', $transfer);

        // dd($transfer->load(['user', 'store', 'warehouse', 'storeRequest', 'transferOrderDetails']));

        return view('pages.transfer.show', [
            'transfer' => $transfer->load(['user', 'store', 'warehouse', 'storeRequest', 'transferOrderDetails.item'])
        ]);
    }

    public function edit(TransferOrder $transfer)
    {
        //prevent umathourized warehouse/user from editing
        Gate::authorize('edit', $transfer);

        return view('pages.transfer.edit', [
            'transfer' => $transfer->load(['user', 'store', 'warehouse', 'storeRequest', 'transferOrderDetails.item'])
        ]);
    }

    public function approval(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:store_requests,id'
        ]);

        //get the store request together with the associated items
        $storeRequestId = $request->id;

        $user = auth()->user();
        $userId = $user->id;
        $warehouseId = $user->warehouse_id;
        $storeRequest = StoreRequest::find($storeRequestId);

        // if ($storeRequest->status === 'approved') {
        //     return response()->json(['message' => 'Store request is already approved'], 400);
        // }

        //policy
        Gate::authorize('approved', $storeRequest);

        //random string
        $randomString = Str::random(10);

        DB::beginTransaction();
        try {
            $total = 0;


            //move item to transfer order
            $transferOrder = TransferOrder::create([
                'store_id' => $storeRequest->store_id,
                'warehouse_id' => $warehouseId,
                'user_id' => $userId,
                'store_request_id' => $storeRequestId,
                'status' => 'pending',
                'order_number' => $randomString,
            ]);

            //move items to transfer order details

            foreach ($storeRequest->storeRequestDetails as $value) {
                $product = Item::findOrFail($value['item_id']);
                $subtotal = $product->price * $value['requested_quantity'];
                $total += $subtotal;
                //insert
                TransferOrderDetail::create([
                    'transfer_order_id' => $transferOrder->id,
                    'item_id' => $value['item_id'],
                    'quantity' => $value['requested_quantity'],
                    'price' => $product->price,
                    'total' => $subtotal,
                ]);

            }


            //approved the store request and make status approved
            $storeRequest->update([
                'status' => 'approved',
                'approval_date' => now(),
                'approved_by' => $userId
            ]);

            //audit trail
            $auditTrail = [
                'user_id' => $userId,
                'warehouse_id' => $warehouseId,
                'ip_address' => $request->ip(),
                'description' => 'Transfer approval',
                'data_before' => json_encode([]),
                'data_after' => json_encode($transferOrder->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();
            return response()->json([
                'message' => 'Store request approved successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['an error occured while approving'], 500);
        }
    }

    public function cancel(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:store_requests,id'
        ]);

        //get the store request together with the associated items
        $storeRequestId = $request->id;

        $user = auth()->user();
        $userId = $user->id;
        $warehouseId = $user->warehouse_id;
        $storeRequest = StoreRequest::find($storeRequestId);

        $originalData = $storeRequest->getOriginal();

        //policy
        Gate::authorize('approved', $storeRequest);


        DB::beginTransaction();
        try {


            //approved the store request and make status approved
            $storeRequest->update([
                'status' => 'cancelled',
                'approval_date' => now(),
                'approved_by' => $userId
            ]);

            //audit trail
            $auditTrail = [
                'user_id' => $userId,
                'warehouse_id' => $warehouseId,
                'ip_address' => $request->ip(),
                'description' => 'store request cancelled',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($storeRequest->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();
            return response()->json([
                'message' => 'Store request cancelled successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['an error occured while approving'], 500);
        }
    }

    //dispatch good
    public function dispatch(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'transferOrderId' => 'required|exists:transfer_orders,id',
        ]);

        // Extract required inputs
        $requestedItems = $validated['items'];
        $requestId = $validated['transferOrderId'];
        $userId = auth()->id();

        // Fetch the existing request
        $transferOrder = TransferOrder::findOrFail($requestId);

        Gate::authorize('dispatched', $transferOrder); //prevent user from dispatching again

        $originalData = $transferOrder->getOriginal();

        DB::beginTransaction();

        try {

            $total = 0;

            // Prepare data for the request update
            $requestItemsData = array_map(function ($item) use (&$total) {
                $product = Item::findOrFail($item['id']);
                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;
                return [

                    'item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $subtotal,

                ];
            }, $requestedItems);

            $requestedData = [
                'user_id' => $userId,
                'status' => 'dispatched',
                'dispatched_by' => $userId,
                'dispatched_date' => now(),
            ];

            // Update the item request
            $transferOrder->update($requestedData);

            // Clear old request details and insert the new ones
            $transferOrder->transferOrderDetails()->delete();
            $transferOrder->transferOrderDetails()->createMany($requestItemsData);

            $auditTrail = [
                'user_id' => auth()->id(),
                'warehouse_id' => auth()->user()->warehouse_id,
                'ip_address' => $request->ip(),
                'description' => 'transfer order dispatch',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($transferOrder->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            return response()->json(['message' => 'Transfer order dispatched successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error while processing request',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function delivered(Request $request)
    {
        $validate = $request->validate([
            'id' => 'required|exists:transfer_orders,id'
        ]);

        $transfer = TransferOrder::findOrFail($validate['id']);
        $userId = auth()->id();

        Gate::authorize('delivered', $transfer); //prevent user from dispatching again

        $originalData = $transfer->getOriginal();
        DB::beginTransaction();
        try {

            $transfer->update([
                'status' => 'delivered',
                'updated_at' => now(),
                'accepted_by' => $userId,
                'accepted_date' => now(),
            ]);

            //deduct from the warehouse inventory
            $warehouseId = $transfer->warehouse_id;

            //loop through warehouse inventories and deduct
            foreach ($transfer->transferOrderDetails as $value) {
                WarehouseInventory::where('item_id', $value['item_id'])
                    ->where('warehouse_id', $warehouseId)
                    ->decrement('quantity', $value['quantity']);
            }

            //loop through store inventory and add
            foreach ($transfer->transferOrderDetails as $value) {
                $itemId = $value['item_id'];
                $quantity = $value['quantity'];
                $storeId = $transfer->store_id;

                // Check if the item already exists in the store inventory
                $existingInventory = DB::table('store_inventories')
                    ->where('item_id', $itemId)
                    ->where('store_id', $storeId)
                    ->first();

                if ($existingInventory) {
                    // If it exists, update the quantity
                    DB::table('store_inventories')
                        ->where('id', $existingInventory->id)
                        ->increment('quantity', $quantity);
                } else {
                    // If it doesn't exist, create a new record
                    DB::table('store_inventories')->insert([
                        'item_id' => $itemId,
                        'store_id' => $storeId,
                        'quantity' => $quantity,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }


            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => $request->ip(),
                'description' => 'transfer order delivered',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($transfer->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();
            return response()->json([
                'message' => 'Transfer delivered successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error while processing request',
            ], 500);
        }


    }

    public function print(Request $request, TransferOrder $transfer)
    {

        if (!$transfer) {
            return back()->with('error', 'it does not exist');
        }

        $transferOrder = $transfer->load(['transferOrderDetails.item', 'warehouse', 'store']);
        $company = Company::first();


        $pdf = FacadePdf::loadView('pages.transfer.print', [
            'transfer' => $transferOrder,
            'company' => $company
        ])->setPaper('a4', 'portrait');

        return $pdf->download($transferOrder->order_number . '.pdf');


    }
}
