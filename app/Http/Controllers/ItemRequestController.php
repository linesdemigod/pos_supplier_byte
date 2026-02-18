<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Warehouse;
use App\Models\ItemRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ItemRequestDetail;
use Illuminate\Support\Facades\DB;

class ItemRequestController extends Controller
{
    public function index()
    {


        return view('pages.itemrequest.index');
    }

    public function create()
    {
        $warehouses = Warehouse::latest()
            ->get();

        return view('pages.itemrequest.create', [
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
            $total = 0;

            $requestData = [
                'user_id' => $userId,
                'reference' => $reference,
                'store_id' => $storeId,
                'warehouse_id' => $warehouseId,
                'status' => 'successful',
            ];

            $requestItemsData = [];
            //loop through the itens and prepare the sales data
            foreach ($saleItems as $item) {
                //find the item
                $product = Item::findOrFail($item['id']);
                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $requestItemsData[] = [

                    'item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $subtotal,
                ];


            }


            $requestData['total'] = $total;

            $itemRequest = ItemRequest::create($requestData); //insert into itemRequests
            foreach ($requestItemsData as $item) {
                $item['item_request_id'] = $itemRequest->id;
            }
            $itemRequest->itemRequestDetails()->createMany($requestItemsData); //insert into sale items

            // commit the transaction
            DB::commit();

            // return a response indicating the order was successfully processed
            return response()->json(['message' => 'Items request processed successfully']);

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
            'requestId' => 'required|exists:item_requests,id'
        ]);

        // Extract required inputs
        $saleItems = $validated['items'];
        $warehouseId = $validated['warehouse'];
        $requestId = $validated['requestId'];
        $userId = auth()->id();

        // Fetch the existing request
        $itemRequest = ItemRequest::findOrFail($requestId);

        DB::beginTransaction();

        try {
            $total = 0;

            // Prepare data for the request update
            $requestItemsData = array_map(function ($item) use (&$total) {
                $product = Item::findOrFail($item['id']); // Ensure the item exists
                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                return [
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $subtotal,
                ];
            }, $saleItems);

            $requestData = [
                'user_id' => $userId,
                'warehouse_id' => $warehouseId,
                'total' => $total,
                'status' => 'successful',
            ];

            // Update the item request
            $itemRequest->update($requestData);

            // Clear old request details and insert the new ones
            $itemRequest->itemRequestDetails()->delete();
            $itemRequest->itemRequestDetails()->createMany($requestItemsData);

            DB::commit();

            return response()->json(['message' => 'Items request processed successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error while processing request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function edit(ItemRequest $item)
    {
        $warehouses = Warehouse::latest()
            ->get();

        // dd($item->total);
        $items = $item->load('itemRequestDetails.item');



        return view('pages.itemrequest.edit', [
            'warehouses' => $warehouses,
            'item' => $item,
            'requestItems' => $items,
        ]);
    }

    public function show(ItemRequest $item)
    {


        return view('pages.itemrequest.show', [
            'requestItems' => $item->load(['itemRequestDetails.item', 'store', 'warehouse', 'user'])
        ]);
    }
}
