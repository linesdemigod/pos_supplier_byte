<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    function index()
    {

        $company = Company::first();

        return view("pages.company.index", compact('company'));
    }

    function create()
    {

        return view('pages.company.create');
    }

    public function store(Request $request)
    {


        $formData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required',
            'address' => 'required',


        ]);

        //check if there is record in company table
        $company = Company::first();

        if ($company) {
            return to_route('company.index')->with('message', 'Company already added! please you can update the info!!');
        }

        $user = auth()->user();
        $userId = $user->id;
        $warehouseId = $user->warehouse_id;
        $storeId = $user->store_id;

        DB::beginTransaction();
        try {



            // add to the db
            $data = Company::create($formData);

            $auditTrail = [
                'user_id' => $userId,
                'ip_address' => $request->ip(),
                'store_id' => $storeId,
                'warehouse_id' => $warehouseId,
                'description' => 'company account creation',
                'data_before' => json_encode([]), // no previous data since it's a new record
                'data_after' => json_encode($data->toArray()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            //commit
            DB::commit();

            return to_route('company.index')->with('message', 'Company added successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'An error occurred while adding the company account.');
        }




    }

    public function edit(Company $company)
    {


        return view('pages.company.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {

        $formData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required',
            'address' => 'required',

        ]);

        // Get the original data before the update
        $originalData = $company->getOriginal();
        $user = auth()->user();
        $userId = $user->id;
        $warehouseId = $user->warehouse_id;
        $storeId = $user->store_id;

        DB::beginTransaction();
        try {

            // Update the hostel
            $company->update($formData);

            // Prepare audit trail data
            $auditTrail = [
                'user_id' => $userId,
                'ip_address' => $request->ip(),
                'store_id' => $storeId,
                'warehouse_id' => $warehouseId,
                'description' => 'company account updated',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($company->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            return to_route('company.index')->with('message', 'Company account updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'An error occurred while updating the company account. ');
        }
    }


    public function destroy(Request $request, Company $company)
    {

        // Get the original data before the update
        $originalData = $company->getOriginal();
        $user = auth()->user();
        $userId = $user->id;
        $warehouseId = $user->warehouse_id;
        $storeId = $user->store_id;

        DB::beginTransaction();
        try {

            // soft delete it
            $company->delete();

            // Prepare audit trail data
            $auditTrail = [
                'user_id' => $userId,
                'ip_address' => $request->ip(),
                'store_id' => $storeId,
                'warehouse_id' => $warehouseId,
                'description' => 'company account deletion',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            return back()->with('message', 'Company account deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('message', 'An error ocurred while deleting the company account');
        }
    }
}
