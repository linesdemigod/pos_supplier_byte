<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\Customer;
use App\Models\Repayment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CreditController extends Controller
{
    public function index()
    {
        return view('pages.credit.index');
    }

    public function summary(Customer $customer)
    {

        //get the date
        $dates = $this->getCurrentMonthRange();

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

        // dd($dates);

        return view('pages.credit.summary', [
            'customer' => $customer,
            'creditSummary' => $creditSummary,
            'outstanding' => $outstanding,
            'dates' => $dates
        ]);
    }

    public function creditDetail(Customer $customer)
    {

        return view('pages.credit.detail', [
            'customer' => $customer
        ]);
    }

    public function creditItemDetail(Credit $credit)
    {

        // $creditItems = $credit->creditOrderItems()->with('item')->get();
        $creditItems = $credit->load(['customer', 'creditItems.item']);


        return view('pages.credit.item-detail', [
            'credit' => $credit,
            'creditItems' => $creditItems,
        ]);

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

    private function getCurrentMonthRange(): array
    {
        // $startOfMonth = Carbon::now()->startOfMonth();
        // $endOfMonth = Carbon::now()->endOfMonth();

        return $monthRange = [
            'start' => now()->startOfMonth()->format('Y-m-d'),
            'end' => now()->endOfMonth()->format('Y-m-d')
        ];
        ;
    }



}
