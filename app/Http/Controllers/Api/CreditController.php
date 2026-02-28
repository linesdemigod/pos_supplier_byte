<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\Repayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreditController extends Controller
{
    public function customerCredits(Request $request)
    {
        $perPage = $request->integer('per_page', 10);
        $search = $request->query('q', '');
        $page = $request->integer('page', 1);

        $customersWithCredits = Customer::whereHas('credits', function ($query) {
            $query->whereIn('status', ['credit', 'partial', 'paid']);
        })
            ->withSum([
                'credits as total_credit_amount' => function ($query) {
                    $query->whereIn('status', ['credit', 'partial', 'paid']);
                }
            ], 'total_amount')
            ->addSelect([
                'total_repaid_amount' => DB::table('repayments')
                    ->selectRaw('COALESCE(SUM(repayments.amount_paid), 0)')
                    ->join('credits', 'credits.id', '=', 'repayments.credit_id')
                    ->whereIn('credits.status', ['credit', 'partial', 'paid'])
                    ->where('repayments.payment_status', 'paid')
                    ->where('repayments.store_id', auth()->user()->store_id)
                    ->whereColumn('credits.customer_id', 'customers.id'),
            ])
            ->search($search)
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        // Compute outstanding balance
        $customersWithCredits->getCollection()->transform(function ($customer) {
            $customer->outstanding = ($customer->total_credit_amount ?? 0) - ($customer->total_repaid_amount ?? 0);
            return $customer;
        });

        return response()->json([
            "customers" => $customersWithCredits
        ], 200);

    }

    public function show(Customer $customer)
    {

        // Gate::authorize('view', $customer);

        //amount 
        $creditSummary = Customer::whereHas('credits', function ($query) {
            $query->whereIn('status', ['credit', 'partial', 'paid']);
        })
            ->withSum([
                'credits as total_credit_amount' => function ($query) {
                    $query->whereIn('status', ['credit', 'partial', 'paid']);
                }
            ], 'total_amount')
            ->addSelect([
                'total_repaid_amount' => DB::table('repayments')
                    ->selectRaw('COALESCE(SUM(repayments.amount_paid), 0)')
                    ->join('credits', 'credits.id', '=', 'repayments.credit_id')
                    ->whereIn('credits.status', ['credit', 'partial', 'paid'])
                    ->where('repayments.payment_status', 'paid')
                    ->where('repayments.store_id', auth()->user()->store_id)
                    ->whereColumn('credits.customer_id', 'customers.id'),
            ])
            ->where('id', $customer->id)
            ->first();


        $outstanding = $creditSummary->total_credit_amount - $creditSummary->total_repaid_amount;


        return response()->json([
            'customer' => $customer,
            'creditSummary' => $creditSummary,
            'outstanding' => $outstanding,
        ], 200);
    }

    public function creditDetail(Request $request, Customer $customer)
    {
        $perPage = $request->integer('perPage', 10);
        $search = $request->input('search', '');

        $credits = Credit::where('customer_id', $customer->id)
            ->search($search)
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'credits' => $credits,
        ], 200);

    }

    public function creditPaymentDetail(Request $request)
    {

        $validated = $request->validate([
            'date_range' => 'required',
            'customer_id' => 'required|exists:customers,id',
        ]);

        [$startDate, $endDate] = $this->parseDateRange($validated['date_range']);

        $repayments = Repayment::where('customer_id', $validated['customer_id'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('store_id', auth()->user()->store_id)
            ->whereIn('payment_status', ['paid', 'partial'])
            // ->with('credit')
            ->latest()
            ->get();


        return response()->json([
            'repayments' => $repayments
        ], 200);
    }


    //request to get customer all repayment
    // public function fetchRepayment(Request $request)
    // {
    //     $customer_id = $request->customerId;
    //     $perPage = $request->query('per_page', 10);
    //     $page = $request->query('page', 1);

    //     // Get the customer
    //     $customer = Customer::find($customer_id);

    //     if (!$customer) {
    //         return response()->json(['message' => 'Customer not found'], 404);
    //     }

    //     // Get payments for the customer's credit orders
    //     // $repayments = Payment::whereHas('order', function ($query) use ($customer_id) {
    //     //     $query->where('customer_id', $customer_id)
    //     //         ->where('payment_status', 'credit')
    //     //         ->where('store_id', auth()->user()->store_id);
    //     // })

    //     // $repayments = RepaymentHistory::where('customer_id', $customer_id)
    //     //     ->where('store_id', auth()->user()->store_id)
    //     //     ->latest()
    //     //     ->paginate($perPage, ['*'], 'page', $page);
    //     // $repayments = RepaymentHistory::where('customer_id', $customer_id)
    //     //     ->where('store_id', auth()->user()->store_id)
    //     //     ->select(DB::raw('DATE(created_at) as created_at'), DB::raw('SUM(amount_paid) as amount_paid'))
    //     //     ->groupBy('created_at')
    //     //     ->orderBy('created_at', 'desc')
    //     //     ->paginate($perPage, ['*'], 'page', $page);

    //     $repayments = RepaymentHistory::where('customer_id', $customer_id)
    //         ->where('store_id', auth()->user()->store_id)
    //         ->whereHas('repayment', function ($q) {
    //             $q->where('payment_status', 'paid');
    //         })
    //         ->select(
    //             'batch_id',
    //             DB::raw('SUM(amount_paid) as amount_paid'),
    //             DB::raw('MIN(created_at) as created_at') // earliest timestamp of the batch
    //         )
    //         ->groupBy('batch_id')
    //         ->orderBy('created_at', 'desc')
    //         ->paginate($perPage, ['*'], 'page', $page);


    //     // Calculate customer outstanding balance
    //     $outstanding = Credit::where('customer_id', $customer_id)
    //         ->where('status', 'credit')
    //         ->where('store_id', auth()->user()->store_id)
    //         ->sum('total_amount')
    //         - Repayment::whereHas('credit', function ($query) use ($customer_id) {
    //             $query->where('customer_id', $customer_id)
    //                 ->whereIn('status', ['credit', 'partial'])
    //                 ->where('store_id', auth()->user()->store_id);
    //         })->sum('amount_paid');

    //     return response()->json([
    //         'repayments' => $repayments->items(),
    //         'current_page' => $repayments->currentPage(),
    //         'last_page' => $repayments->lastPage(),
    //         'total' => $repayments->total(),
    //         'per_page' => $repayments->perPage(),
    //         'customer' => $customer,
    //         'outstanding' => $outstanding
    //     ], 200);
    // }

    //pay repayment
    public function payRepayment(Request $request)
    {
        $request->validate([
            'customer' => 'required|exists:customers,id',
            'amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/']
        ]);

        $customerId = $request->input('customer');
        $paymentAmount = (float) $request->input('amount');
        $now = now();
        $user = auth()->user();

        $customer = Customer::find($customerId);
        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        // Calculate customer's total outstanding (sum of credit orders minus total payments)
        $totalCredit = Credit::where('customer_id', $customerId)
            ->whereIn('status', ['credit', 'partial'])
            ->where('store_id', auth()->user()->store_id)
            ->sum('total_amount');

        $totalPaid = Repayment::whereHas('credit', function ($q) use ($customerId) {
            $q->where('customer_id', $customerId)
                ->where('status', 'credit')
                ->where('store_id', auth()->user()->store_id);
        })->sum('amount_paid');

        $outstanding = $totalCredit - $totalPaid;

        if ($outstanding <= 0) {
            return response()->json(['error' => 'Customer has no outstanding balance'], 400);
        }

        if ($paymentAmount > $outstanding) {
            return response()->json(['error' => 'Amount exceeds customer\'s outstanding balance'], 400);
        }


        // Generate one batch_id for this repayment session
        $batchId = Str::uuid();
        $reference = rand(1000, 99999) . strtoupper(Str::random(8));

        DB::beginTransaction();

        try {
            // Fetch unpaid orders (FIFO)
            $orders = Credit::where('customer_id', $customerId)
                ->whereIn('status', ['credit', 'partial'])
                ->where('store_id', $user->store_id)
                ->orderBy('created_at', 'asc')
                ->withSum('repayments', 'amount_paid')
                ->get();

            $remainingAmount = $paymentAmount;

            foreach ($orders as $order) {
                if ($remainingAmount <= 0)
                    break;

                // Calculate order's outstanding amount
                // $orderPaid = $order->payments()->sum('amount_paid');
                $orderPaid = $order->repayments_sum_amount_paid ?? 0;
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
                $payment = Repayment::create([
                    'credit_id' => $order->id,
                    'user_id' => $user->id,
                    'amount_paid' => $paymentToApply,
                    'store_id' => $user->store_id,
                    'payment_method' => 'cash',
                    'customer_id' => $customerId,
                    'reference' => $reference,
                    'date_paid' => now()
                ]);

                // //insert to repayment History
                // RepaymentHistory::create([
                //     'customer_id' => $customerId,
                //     'store_id' => $user->store_id,
                //     'amount_paid' => $payment->amount_paid,
                //     'repayment_id' => $payment->id,
                //     'batch_id' => $batchId,
                // ]);


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
                    'customer_id' => $customerId,
                    'amount_paid' => $paymentAmount,
                    'batch_id' => $batchId,
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Payment submitted successfully',
                'repayment' => $payment,
                'orderOutstanding' => $orderOutstanding
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function receipt()
    {


        return view('pages.credit.receipt');
    }

    public function showReceipt(Credit $credit)
    {

        $order = $credit->load(['customer', 'creditOrder.creditOrderItems.item', 'user']);

        return view('pages.credit.show-credit', [
            'sale' => $order,
        ]);
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
}
