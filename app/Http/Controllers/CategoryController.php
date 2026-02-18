<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Imports\CategoryImport;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{
    public function index()
    {

        return view('pages.category.index');
    }

    public function create()
    {

        return view('pages.category.create');
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'description' => 'nullable|sometimes',
            'category_code' => ['required', Rule::unique('categories', 'category_code')],
        ]);

        $user = auth()->user();
        $userId = $user->id;
        $warehouseId = $user->warehouse_id;
        $storeId = $user->store_id;

        DB::beginTransaction();
        try {
            $data = Category::create($validate);

            $auditTrail = [
                'user_id' => $userId,
                'ip_address' => $request->ip(),
                'store_id' => $storeId,
                'warehouse_id' => $warehouseId,
                'description' => 'category creation',
                'data_before' => json_encode([]), // no previous data since it's a new record
                'data_after' => json_encode($data->toArray()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();
            return back()->with('message', 'category added successfully');
        } catch (\Exception $e) {
            Db::rollBack();
            return back()->with('error', 'Error adding category');
        }

    }

    public function edit(Category $category)
    {



        return view('pages.category.edit', [
            'category' => $category
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validate = $request->validate([
            'name' => 'required',
            'description' => 'nullable|sometimes',
            'category_code' => ['required', Rule::unique('categories', 'category_code')->ignore($category->id)],
        ]);

        // Get the original data before the update
        $originalData = $category->getOriginal();
        $user = auth()->user();
        $userId = $user->id;
        $warehouseId = $user->warehouse_id;
        $storeId = $user->store_id;

        DB::beginTransaction();
        try {

            $category->update($validate);

            $auditTrail = [
                'user_id' => $userId,
                'ip_address' => $request->ip(),
                'store_id' => $storeId,
                'warehouse_id' => $warehouseId,
                'description' => 'category update',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($category->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            return to_route('category.index')->with('message', 'category updated successfully');
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
            Excel::import(new CategoryImport, $request->file('excel'));

            return response()->json(['success' => 'Categories added successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
}
