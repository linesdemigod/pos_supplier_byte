<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Store;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{

    public function index()
    {
        $stores = Store::latest()
            ->paginate(10);

        return view('pages.store.index', [
            'stores' => $stores,
        ]);
    }

    public function create()
    {

        return view('pages.store.create');
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'location' => 'required',
            'phone' => 'nullable|sometimes',
        ]);

        $user = auth()->user();
        $storeId = $user->store_id;
        $userId = $user->id;
        $companyId = Company::first()->id;
        $validate['company_id'] = $companyId;

        DB::beginTransaction();
        try {
            $data = Store::create($validate);

            $auditTrail = [
                'user_id' => $userId,
                'store_id' => $storeId,
                'ip_address' => $request->ip(),
                'description' => 'store creation',
                'data_before' => json_encode([]), // no previous data since it's a new record
                'data_after' => json_encode($data->toArray()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();
            return back()->with('message', 'store added successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error adding store');
        }

    }

    public function edit(Store $store)
    {



        return view('pages.store.edit', [
            'store' => $store
        ]);
    }

    public function update(Request $request, Store $store)
    {
        $validate = $request->validate([
            'name' => 'required',
            'location' => 'required',
            'phone' => 'nullable|sometimes',
        ]);

        // Get the original data before the update
        $originalData = $store->getOriginal();
        $companyId = Company::first()->id;
        $validate['company_id'] = $companyId;

        DB::beginTransaction();
        try {

            $store->update($validate);

            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => $request->ip(),
                'description' => 'store update',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($store->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            return to_route('store.index')->with('message', 'store updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['an error while updating']);
        }

    }

    public function destroy(Request $request, Store $store)
    {

        $originalData = $store->getOriginal();
        DB::beginTransaction();
        try {

            $store->delete();

            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => $request->ip(),
                'description' => 'store deletion',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($store->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();
            return back()->with('message', 'Store deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'an error occured while deleting');
        }


    }
}
