<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Credit;
use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalesPointPermission;
use App\Models\Shift;
use App\Models\StoreInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ShopController extends Controller
{
    public function getItems(Request $request)
    {
        $categoryId = $request->integer('category'); // null if invalid

        $items = Item::with(['category'])
            ->where('is_available', true)
            ->when($categoryId !== null && $categoryId !== 0, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->latest()
            ->get();



        return response()->json([
            'items' => $items,
        ]);
    }


    public function shopItems(Request $request)
    {
        $searchQuery = $request->input('item');
        $categoryId = $request->input('category');
        $storeId = $request->input('storeId');

        $items = Item::whereHas('storeInventories', function ($query) use ($storeId) {
            $query->where('store_id', $storeId);
        })
            ->search($searchQuery, $categoryId)
            ->with('storeInventories')
            ->latest() // Order by latest
            ->get();


        return response()->json([
            'items' => $items
        ], 200);
    }


    public function getCategory()
    {
        $categories = Category::latest()->get();

        return response()->json([
            'categories' => $categories,
        ], 200);
    }

    //get the stock level
    public function getStockLevel(Request $request)
    {
        $itemId = $request->input('id');
        $storeId = auth()->user()->store_id;

        $quantityLeft = StoreInventory::where('store_id', $storeId)
            ->where('item_id', $itemId)
            ->value('quantity');

        $allowedNegative = SalesPointPermission::where('permission_name', 'allow_negative')->value('status');

        return response()->json([
            'quantity' => $quantityLeft,
            'allowed_negative' => $allowedNegative,
        ], 200);
    }


    //place order
    public function placeOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'reference' => ['required', 'numeric', Rule::unique('sales', 'reference')],
            'credit' => 'boolean',
        ]);

        $storeId = auth()->user()->store_id;

        // check if price edit is allowed; if price edit is true then use the price from user
        $priceEditStatus = SalesPointPermission::where('permission_name', 'price_edit')->value('status');


        $saleItems = $request->items;
        $reference = $request->reference;
        $discount = $request->discount;
        $customer = $request->customer;
        $isCredit = $request->credit;
        $shiftId = $this->userShift();


        DB::beginTransaction();
        try {
            $total = 0;

            $saleData = [
                'user_id' => auth()->id(),
                'discount' => $discount,
                'reference' => $reference,
                'store_id' => $storeId,
                'customer_id' => $customer,
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'shift_id' => $shiftId,
            ];

            $saleItemData = [];
            //loop through the itens and prepare the sales data
            foreach ($saleItems as $item) {
                //find the item
                $product = Item::findOrFail($item['id']);

                // Use user-provided price if price editing is allowed; otherwise, use the product's price
                $price = ($priceEditStatus) ? $item['price'] : $product->price;

                $subtotal = $price * $item['quantity'];
                $total += $subtotal;

                $saleItemData[] = [

                    'item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'total' => $subtotal,
                ];

                // $this->reduceQuantity($item['id'], $item['quantity']); //reduce stock
            }

            $grandTotal = $total - $discount;

            $saleData['subtotal'] = $total;
            $saleData['grandtotal'] = $grandTotal;

            if ($isCredit) {
                $sale = $this->creditOrderProcess($saleData, $saleItemData, $saleItems);
            } else {
                $sale = $this->createNewOrder($saleData, $saleItemData, $saleItems);
            }

            // $sale = Sale::create($saleData); //insert into sales
            // foreach ($saleItemData as $item) {
            //     $item['sale_id'] = $sale->id;
            // }
            // $sale->saleItems()->createMany($saleItemData); //insert into sale items

            $description = $isCredit ? "Credit Order Placed" : "Paid Order Placed";

            $auditTrail = [
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'store_id' => $storeId,
                'warehouse_id' => null,
                'description' => $description,
                'data_before' => json_encode([]), // no previous data since it's a new record
                'data_after' => json_encode($sale->toArray()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            // commit the transaction
            DB::commit();

            // return a response indicating the order was successfully processed
            return response()->json(['message' => 'Order processed successfully', 'orderId' => $sale->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error while processing order ' . $e->getMessage()], 500);
        }
    }

    private function createNewOrder(array $orderData, array $orderItemData, array $orderItems)
    {
        $sale = Sale::create($orderData);

        // Attach order ID to each order item
        foreach ($orderItemData as &$item) {
            $item['sale_id'] = $sale->id;
        }

        // Create all order items
        $sale->saleItems()->createMany($orderItemData);

        // Reduce stock only after successful item creation
        foreach ($orderItems as $item) {
            $this->reduceQuantity($item['id'], $item['quantity']);
        }

        return $sale;

    }

    private function creditOrderProcess(array $orderData, array $orderItemData, array $orderItems)
    {


        //total amount 
        $total = round($orderData['subtotal'] - $orderData['discount'], 2);

        $credit = Credit::create([
            'user_id' => $orderData['user_id'],
            'customer_id' => $orderData['customer_id'],
            'discount' => $orderData['discount'],
            'subtotal' => $orderData['subtotal'],
            'total_amount' => $total,
            'reference' => $orderData['reference'],
            'shift_id' => $orderData['shift_id'],
        ]);



        // Attach order ID to each order item
        foreach ($orderItemData as &$item) {
            $item['credit_id'] = $credit->id;
        }

        // Create all order items
        $credit->creditItems()->createMany($orderItemData);


        // Reduce stock only after successful item creation
        foreach ($orderItems as $item) {
            $this->reduceQuantity($item['id'], $item['quantity']);
        }


        return $credit;
    }


    public function fetchOrderById(Request $request)
    {
        $validated = $request->validate([
            'orderId' => 'required|exists:sales,id'
        ]);

        $order = Sale::where('id', $validated['orderId'])
            ->where('user_id', auth()->id())
            ->first();


        $orders = SaleItem::with('item')
            ->where('sale_id', '=', $order->id)
            ->latest()
            ->get();



        return response()->json([
            'orderItems' => $orders,
            'orderTaxes' => [],
        ]);
    }

    private function userShift()
    {

        $userId = auth()->id();

        $shift = Shift::where('user_id', $userId)->latest('id')->value('id');

        return $shift;

    }


    public function reduceQuantity($itemId, $quantity)
    {
        StoreInventory::
            where('store_id', auth()->user()->store_id)
            ->where('item_id', $itemId)
            ->update(['quantity' => DB::raw('quantity - ' . $quantity)]);
    }


}
