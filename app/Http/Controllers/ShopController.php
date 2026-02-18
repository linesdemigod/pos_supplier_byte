<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Sale;
use App\Models\Shift;
use App\Models\Company;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Customer;
use App\Models\HoldSale;
use App\Models\DailySale;
use App\Models\MonthlySale;
use Illuminate\Support\Str;
use App\Models\HoldSaleItem;
use Illuminate\Http\Request;
use App\Models\StoreInventory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\SalesPointPermission;
use Illuminate\Support\Facades\Cookie;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;




class ShopController extends Controller
{
    public function index()
    {

        //get categories
        $user = auth()->user();
        $store = $user->store_id;
        // get categories
        $categories = Category::latest()->get();

        //get the last daily sales
        // $lastDailySale = DailySale::where('store_id', $store)->latest('id')->first();





        // $customers = Customer::latest()
        //     ->where('store_id', $store)
        //     ->get();

        return view('pages.shop.index', [
            'categories' => $categories,
            // 'lastDailySale' => $lastDailySale,
            // 'customers' => $customers,
        ]);

    }

    public function getItem(Request $request)
    {
        $searchQuery = $request->input('item');
        $categoryId = $request->input('category');
        $storeId = auth()->user()->store_id;

        $items = Item::whereHas('storeInventories', function ($query) use ($storeId) {
            $query->where('store_id', $storeId);
        })
            ->search($searchQuery, $categoryId)
            ->with('storeInventories')
            ->latest() // Order by latest
            ->get();

        $permissions = SalesPointPermission::whereIn('permission_name', ['allow_negative', 'price_edit'])
            ->pluck('status', 'permission_name');

        return response()->json([
            'items' => $items,
            'allowedNegative' => $permissions['allow_negative'] ?? null,
            'priceEdit' => $permissions['price_edit'] ?? null,
        ], 200);
    }

    public function placeOrder(Request $request)
    {

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'reference' => ['required', 'numeric', Rule::unique('sales', 'reference')],
        ]);

        $storeId = auth()->user()->store_id;
        // $lastSale = DailySale::where('store_id', $storeId)->latest('id')->first();
        // $lastMonthSale = MonthlySale::where('store_id', $storeId)->latest('id')->first();

        // check if price edit is allowed; if price edit is true then use the price from user
        $priceEditStatus = SalesPointPermission::where('permission_name', 'price_edit')->value('status');


        $saleItems = $request->items;
        $reference = $request->reference;
        $discount = $request->discount;
        $customer = $request->customer;


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

                $this->reduceQuantity($item['id'], $item['quantity']); //reduce stock
            }

            $grandTotal = $total - $discount;

            $saleData['subtotal'] = $total;
            $saleData['grandtotal'] = $grandTotal;

            $sale = Sale::create($saleData); //insert into sales
            foreach ($saleItemData as $item) {
                $item['sale_id'] = $sale->id;
            }
            $sale->saleItems()->createMany($saleItemData); //insert into sale items

            $auditTrail = [
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'store_id' => $storeId,
                'warehouse_id' => null,
                'description' => 'Order sales',
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
            return response()->json(['message' => 'Order processed successfully', 'id' => $sale->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error while processing order ' . $e->getMessage()], 500);
        }


    }

    public function printReceipt($id)
    {
        try {

            $saleId = $id;
            $store_id = auth()->user()->store_id;
            $company = Company::first();

            $sale = Sale::with(['saleItems', 'store', 'customer', 'user'])
                ->withCount('saleItems')
                ->where('id', $saleId)
                ->where('store_id', $store_id)
                ->first();




            //   $orderData = Order::find($id);
            //   $branch = Branch::find($branch_id);

            // dd($orderProductsData->product);
            // Calculate paper height dynamically
            $baseHeight = 360; // Base height for fixed elements
            $itemHeight = 40;  // Estimated height per item
            $dynamicHeight = $baseHeight + ($sale->sale_items_count * $itemHeight);

            $pdf = FacadePdf::loadView('pages.shop.receipt', [
                'sale' => $sale,
                'company' => $company,

            ])->setPaper([0, 0, 227, $dynamicHeight], 'portrait');

            return $pdf->download('order-' . $sale->reference . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }



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

    public function priceEdit()
    {
        $priceEditStatus = SalesPointPermission::where('permission_name', 'price_edit')->value('status');

        return response()->json([
            'priceEditStatus' => $priceEditStatus,
        ], 200);
    }

    public function holdItem(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $orderItems = $request->input('items');
            $grandTotal = 0;

            // prepare the order data
            $holdSaleData = [];

            // loop through the items and prepare the order product data
            $holdSaleItemsData = [];
            foreach ($orderItems as $item) {
                $product = Item::findOrFail($item['id']);
                $subtotal = $product->price * $item['quantity'];
                $grandTotal += $subtotal;
                $holdSaleItemsData[] = [
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'rate' => $product->price,
                    'subtotal' => $subtotal,

                ];
            }

            // set the subtotal and grandtotal in the order data
            $holdSaleData['total'] = $grandTotal;
            $holdSaleData['store_id'] = auth()->user()->store_id;

            // create the order
            $holdSale = HoldSale::create($holdSaleData);

            foreach ($holdSaleItemsData as $item) {
                $item['hold_sale_id'] = $holdSale->id;
            }

            // associate the order products with the order
            $holdSale->holdSaleItems()->createMany($holdSaleItemsData);

            DB::commit();

            // return a response indicating the order was successfully processed
            return response()->json(['message' => 'Order hold successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error processing order' . $e->getMessage()], 500);

        }
    }

    public function releaseItem(Request $request)
    {

        $id = $request->input('id');

        $getHoldItems = HoldSaleItem::with('item')
            ->where('hold_sale_id', $id)
            ->get();


        if ($getHoldItems) {
            $hold = HoldSale::findOrFail($id);
            $hold->delete();
        }

        return response()->json([
            'items' => $getHoldItems,
        ], 200);
    }

    public function fetchHoldItems()
    {

        $items = HoldSale::latest()
            ->get();

        return response()->json([
            'items' => $items,
        ], 200);
    }

    public function reduceQuantity($itemId, $quantity)
    {
        StoreInventory::
            where('store_id', auth()->user()->store_id)
            ->where('item_id', $itemId)
            ->update(['quantity' => DB::raw('quantity - ' . $quantity)]);
    }

    public function get_end_day_cookie()
    {
        $endDayCookie = Cookie::get('day_cookie');
        $data = '';

        $data = $endDayCookie === null ? "" : $endDayCookie;
        // if ($endDayCookie === null) {
        //     $data = '';
        // } else {
        //     $data = $endDayCookie;
        // }

        return $data;
    }
}
