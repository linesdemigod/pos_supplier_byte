<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\AuditLog;
use App\Models\StoreInventory;
use App\Models\Supplier;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\SupplierPayment;
use Illuminate\Validation\Rule;
use App\Models\SupplierPurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SupplierController extends Controller
{
    public function index()
    {

        return view('pages.supplier.index');
    }
    public function create()
    {

        return view('pages.supplier.create');
    }

    public function purchase()
    {
        // Gate::authorize('view', $supplier);

        return view('pages.supplier.purchase', [
            // 'supplier' => $supplier
        ]);
    }


    public function getSupplier(Request $request)
    {

        $name = $request->input('name');

        $suppliers = Supplier::search($name)
            ->get();

        return response()->json([
            'suppliers' => $suppliers,
        ], 200);

    }

    public function savePurchase(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.costPrice' => 'required|numeric|regex:/^\d+(\.\d+)?$/',
            'items.*.unitType' => 'required|in:box,unit',
            'items.*.conversionRate' => 'required|integer',
            'items.*.totalUnitAdded' => 'required|integer',
            'items.*.subtotal' => 'required|numeric|regex:/^\d+(\.\d+)?$/',
            'reference' => ['required', 'numeric', Rule::unique('supplier_purchases', 'reference')],
            'supplier' => 'required|exists:suppliers,id'
        ], );

        $purchaseItems = $request->input('items');
        $reference = $request->reference . strtoupper(Str::random(8));
        $supplier = $request->input('supplier');
        $user = auth()->user();
        $now = now();

        DB::beginTransaction();

        try {

            $total = 0;
            $purchaseItemData = [];

            foreach ($purchaseItems as $item) {

                $total += $item['subtotal'];

                $purchaseItemData[] = [
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'purchase_unit_type' => $item['unitType'],
                    'cost_price' => $item['costPrice'],
                    'conversion_rate' => $item['conversionRate'],
                    'total_units_added' => $item['totalUnitAdded'],
                    'subtotal' => $item['subtotal'],
                ];
            }

            $purchaseData = [
                'user_id' => $user->id,
                'reference' => $reference,
                'supplier_id' => $supplier,
                'total_amount' => $total,
                'store_id' => $user->store_id,
                'status' => 'unpaid'

            ];

            $purchase = SupplierPurchase::create($purchaseData);

            foreach ($purchaseItemData as &$item) {
                $item['supplier_purchase_id'] = $purchase->id;
            }

            $purchase->supplierPurchaseItems()->createMany($purchaseItemData);


            $auditTrail = [
                'user_id' => $user->id,
                'store_id' => $user->store_id,
                'ip_address' => request()->ip(),
                'description' => 'supplier purchase item insertion',
                'data_before' => json_encode([]),
                'data_after' => json_encode($purchase->toArray()),
                'created_at' => $now,
                'updated_at' => $now,
            ];



            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();
            return response()->json([
                'message' => 'Purchase saved successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'an error occurred ' . $e->getMessage()
            ], 500);
        }


    }

    public function show(Supplier $supplier)
    {

        Gate::authorize('view', $supplier);
        //amount 
        $purchaseSummary = Supplier::whereHas('supplierPurchases', function ($query) {
            $query->whereIn('status', ['unpaid', 'partial', 'paid']);
        })
            ->withSum([
                'supplierPurchases as total_purchase_amount' => function ($query) {
                    $query->whereIn('status', ['unpaid', 'partial', 'paid']);
                }
            ], 'total_amount')
            ->addSelect([
                'total_repaid_amount' => DB::table('supplier_payments')
                    ->selectRaw('COALESCE(SUM(supplier_payments.amount_paid), 0)')
                    ->join('supplier_purchases', 'supplier_purchases.id', '=', 'supplier_payments.supplier_purchase_id')
                    ->whereIn('supplier_purchases.status', ['unpaid', 'partial', 'paid'])
                    ->where('supplier_payments.status', 'paid')
                    ->where('supplier_payments.store_id', auth()->user()->store_id)
                    ->whereColumn('supplier_purchases.supplier_id', 'suppliers.id'),
            ])
            ->where('id', $supplier->id)
            ->first();


        $outstanding = ($purchaseSummary->total_purchase_amount ?? 0) - ($purchaseSummary->total_repaid_amount ?? 0);
        // $outstanding = 0;

        $dateRange = now()->startOfMonth()->format('Y-m-d');

        return view('pages.supplier.show', [
            'supplier' => $supplier,
            'purchaseSummary' => $purchaseSummary,
            'outstanding' => $outstanding,
            'dateRange' => $dateRange,
        ]);
    }

    public function payment(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'amount_paid' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'payment_method' => 'required|in:cash,momo,cheque,bank_transfer,card',
        ]);

        $supplierId = $request->input('supplier_id');
        $paymentAmount = (float) $request->input('amount_paid');
        $now = now();
        $user = auth()->user();
        $paymentMethod = $request->input('payment_method');

        // Calculate supplier's total outstanding (sum of credit orders minus total payments)
        $totalPurchase = SupplierPurchase::where('supplier_id', $supplierId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->where('store_id', $user->store_id)
            ->sum('total_amount');

        $totalPaid = SupplierPayment::whereHas('supplierPurchase', function ($q) use ($supplierId) {
            $q->where('supplier_id', $supplierId)
                ->whereIn('status', ['unpaid, partial'])
                ->where('store_id', auth()->user()->store_id);
        })->sum('amount_paid');

        $outstanding = $totalPurchase - $totalPaid;

        if ($outstanding <= 0) {
            return response()->json(['error' => 'There is no outstanding balance'], 400);
        }

        if ($paymentAmount > $outstanding) {
            return response()->json(['error' => 'Amount exceeds the outstanding balance'], 400);
        }


        $reference = rand(1000, 99999) . strtoupper(Str::random(8));

        DB::beginTransaction();

        try {
            // Fetch unpaid orders (FIFO)
            $orders = SupplierPurchase::where('supplier_id', $supplierId)
                ->whereIn('status', ['unpaid', 'partial'])
                ->where('store_id', $user->store_id)
                ->orderBy('created_at', 'asc')
                ->withSum('supplierPayments', 'amount_paid')
                ->get();

            $remainingAmount = $paymentAmount;

            foreach ($orders as $order) {
                if ($remainingAmount <= 0)
                    break;

                // Calculate order's outstanding amount
                // $orderPaid = $order->payments()->sum('amount_paid');
                $orderPaid = $order->supplier_payments_sum_amount_paid ?? 0;
                $orderOutstanding = $order->total_amount - $orderPaid;

                if ($remainingAmount >= $orderOutstanding) {
                    // Full payment
                    $paymentToApply = $orderOutstanding;
                    $order->status = 'paid';
                    $order->save();


                } else {
                    // Partial payment
                    $paymentToApply = $remainingAmount;
                    $order->status = 'partial';
                    $order->save();
                }

                // Create a payment record linked to this order
                $payment = SupplierPayment::create([
                    'supplier_purchase_id' => $order->id,
                    'user_id' => $user->id,
                    'amount_paid' => $paymentToApply,
                    'store_id' => $user->store_id,
                    'payment_method' => $paymentMethod,
                    'supplier_id' => $supplierId,
                    'reference' => $reference,
                    'status' => 'paid',
                ]);


                $remainingAmount -= $paymentToApply;
            }

            // Audit log
            AuditLog::create([
                'user_id' => $user->id,
                'store_id' => $user->store_id,
                'ip_address' => request()->ip(),
                'description' => 'Credit Payment',
                'data_before' => json_encode([]),
                'data_after' => json_encode([
                    'supplier_id' => $supplierId,
                    'amount_paid' => $paymentAmount,
                    'reference' => $reference,
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Payment submitted successfully',
                'payment' => $payment,
                'orderOutstanding' => $orderOutstanding,
                'amountPaid' => $paymentAmount
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }


    }

    public function purchaseDetail(Supplier $supplier)
    {

        return view('pages.supplier.detail', [
            'supplier' => $supplier
        ]);
    }

    public function purchaseItemDetail(SupplierPurchase $purchase)
    {

        // $creditItems = $credit->creditOrderItems()->with('item')->get();
        $purchaseItems = $purchase->load(['supplier', 'supplierPurchaseItems.item']);


        return view('pages.supplier.item-detail', [
            'purchase' => $purchase,
            'purchaseItems' => $purchaseItems,
        ]);

    }

    public function paymentDetail(Request $request)
    {

        $validated = $request->validate([
            // 'date_range' => 'required',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        // [$startDate, $endDate] = $this->parseDateRange($validated['date_range']);
        // $startDate = '2026-01-11';
        // $endDate = '2026-01-11';
        $startDate = $validated['date_from'];
        $endDate = $validated['date_to'];

        $payments = SupplierPayment::where('supplier_id', $validated['supplier_id'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('store_id', auth()->user()->store_id)
            ->where('status', 'paid')
            ->latest()
            ->get();
        // ->groupBy('reference');


        return response()->json([
            'payments' => $payments,
        ], 200);
    }

    protected function parseDateRange(?string $dateRange): array
    {
        if (!$dateRange) {
            return [null, null];
        }

        $dates = explode('to', $dateRange);

        if (count($dates) < 2) {
            return [null, null];
        }

        $startDate = trim($dates[0]);
        $endDate = trim($dates[1]);

        try {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
        } catch (\Exception $e) {
            return [null, null];
        }

        return [$startDate, $endDate];
    }

    private function getCurrentMonthRange(): string
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        return $startOfMonth->format('Y-m-d') . ' to ' . $endOfMonth->format('Y-m-d');
    }

    public function searchItem(Request $request)
    {

        $searchQuery = $request->input('item');


        $items = StoreInventory::with('item')
            ->search($searchQuery)
            ->latest() // Order by latest
            ->get();

        return response()->json(['items' => $items,], 200);

    }
}
