<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Imports\CustomerImport;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{


    public function index()
    {

        return view('pages.customer.index');
    }

    public function create()
    {

        return view('pages.customer.create');
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'location' => 'required|string|max:255',
        ]);

        // Add user and store details to the data
        $user = auth()->user();
        $validate['user_id'] = $user->id;
        $validate['store_id'] = $user->store_id;

        DB::beginTransaction();

        try {
            // Create a new customer
            $data = Customer::create($validate);

            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => $request->ip(),
                'description' => 'customer creation',
                'data_before' => json_encode([]), // no previous data since it's a new record
                'data_after' => json_encode($data->toArray()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Customer created successfully',
                    'customer' => $data,
                ], 200);
            }

            return back()->with('message', 'Customer created successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'An error occurred while creating the customer',
                    'details' => $e->getMessage(), // Include the exception message for debugging
                ], 500);
            }

            return back()->with('error', 'An error occurred while creating the customer');
        }
    }



    public function edit(Customer $customer)
    {



        return view('pages.customer.edit', [
            'customer' => $customer
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'location' => 'required|string|max:255',
        ]);

        // Get the original data before the update
        $originalData = $customer->getOriginal();

        DB::beginTransaction();
        try {

            $customer->update($validate);

            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => $request->ip(),
                'description' => 'customer update',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($customer->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            return to_route('customer.index')->with('message', 'customer updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['an error while updating']);
        }

    }

    public function excelImport(Request $request)
    {

        $request->validate([
            'excel' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new CustomerImport, $request->file('excel'));

            return response()->json(['success' => 'Customers added successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

    }

    public function getCustomers(Request $request)
    {
        $name = $request->input('name');
        $store_id = auth()->user()->store_id;

        $customers = Customer::
            where('store_id', $store_id)
            ->search($name)
            ->get();

        return response()->json([
            'customers' => $customers,
        ], 200);
    }
}
