<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Store;
use App\Models\AuditLog;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {

        $users = User::with(['store', 'warehouse'])
            ->latest()->get();


        return view("pages.user.index", compact('users'));
    }

    public function create()
    {


        $roles = Role::get();

        return view('pages.user.create', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {


        $formData = $request->validate([
            'role' => 'required|not_in:0',
            'name' => 'required|string|max:255',
            'store_id' => 'nullable|sometimes|exists:stores,id',
            'warehouse_id' => 'nullable|sometimes|exists:warehouses,id',
            'username' => ['required', Rule::unique('users', 'username')],
            'password' => 'required|confirmed',


        ], [
            'store_id.exists' => 'Selected store does not exist',
            'warehouse_id.exists' => 'Selected warehouse does not exist',
        ]);

        if (!$request->filled('store_id') && !$request->filled('warehouse_id')) {
            return response()->json(['errors' => ['branch' => 'You must select a branch.']], 422);
        }

        $formData['status'] = 'active';

        $user = auth()->user();
        $storeId = $user->store_id ?? null;
        $warehouseId = $user->warehouse_id ?? null;

        DB::beginTransaction();
        try {



            // add to the db
            $data = User::create($formData);

            $data->assignRole($formData['role']);

            $auditTrail = [
                'user_id' => $user->id,
                'store_id' => $storeId,
                'warehouse_id' => $warehouseId,
                'ip_address' => $request->ip(),
                'description' => 'User account creation',
                'data_before' => json_encode([]), // no previous data since it's a new record
                'data_after' => json_encode($data->toArray()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            //commit
            DB::commit();

            return response()->json([
                'message' => 'User created successfully'
            ], 200);
            // return to_route('user.index')->with('success', 'User added successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error creating user account '
            ], 500);
            // return back()->with('error', 'An error occurred while adding the user account.' . $e->getMessage());
        }




    }

    public function edit(User $user)
    {

        $warehouseId = $user->warehouse_id;
        $storeId = $user->store_id;


        $roles = Role::get();

        return view('pages.user.edit', [
            'user' => $user->load(['store', 'warehouse']),
            'roles' => $roles,
            'warehouseId' => $warehouseId,
            'storeId' => $storeId,
        ]);
    }

    public function update(Request $request, User $user)
    {

        $formData = $request->validate([
            'role' => 'required',
            'name' => 'required|string|max:255',
            'username' => ['required', Rule::unique('users', 'username')->ignore($user->id)],
            'store_id' => 'nullable|sometimes|exists:stores,id',
            'warehouse_id' => 'nullable|sometimes|exists:warehouses,id',
            'password' => 'nullable|min:6'


        ], [
            'store_id.exists' => 'Selected store does not exist',
            'warehouse_id.exists' => 'Selected warehouse does not exist',
        ]);


        if (!$request->filled('store_id') && !$request->filled('warehouse_id')) {
            return response()->json(['errors' => ['branch' => 'You must select a branch.']], 422);
        }

        // Get the original data before the update
        $originalData = $user->getOriginal();
        $userdata = auth()->user();
        $storeId = $userdata->store_id ?? null;
        $warehouseId = $userdata->warehouse_id ?? null;

        if ($request->filled('store_id')) {
            $formData['warehouse_id'] = null;

        } elseif ($request->filled('warehouse_id')) {

            $formData['store_id'] = null;
        }

        if ($request->filled('password')) {
            $formData['password'] = $request->password;
        } else {
            unset($formData['password']);
        }

        DB::beginTransaction();
        try {

            // Update the hostel
            $user->update($formData);

            //remove and reassign role
            $user->roles()->detach();
            $user->assignRole($formData['role']);



            // Prepare audit trail data
            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => $storeId,
                'warehouse_id' => $warehouseId,
                'ip_address' => $request->ip(),
                'description' => 'user account updated',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($user->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();
            return response()->json([
                'message' => 'User updated successfully',
            ], 200);
            // return to_route('user.home')->with('success', 'User account updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'An error occurred while updating '
            ], 500);
        }
    }


    public function destroy(Request $request, User $user)
    {

        // Get the original data before the update
        $originalData = $user->getOriginal();
        $user = auth()->user();
        $storeId = $user->store_id ?? null;
        $warehouseId = $user->warehouse_id ?? null;

        DB::beginTransaction();
        try {

            // soft delete it
            $user->delete();

            // Prepare audit trail data
            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => $storeId,
                'warehouse_id' => $warehouseId,
                'ip_address' => $request->ip(),
                'description' => 'user account deletion',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();

            return back()->with('message', 'User account deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('message', 'An error ocurred while deleting the user account ' . $e->getMessage());
        }
    }

    function account_status(Request $request)
    {

        $formData = $request->validate([
            'id' => 'required|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        $user = User::where('id', $formData['id'])->first();


        //update user account status
        $user->update([
            'status' => $formData['status']
        ]);

        return response()->json([
            'status' => 'updated successfully',
        ], 200, );
    }

    public function getBranchs(Request $request)
    {

        $validate = $request->validate([
            'id' => 'required|in:0,1'
        ]);

        $branches = match ($validate['id']) {
            '0' => Store::latest()
                ->get(),
            '1' => Warehouse::latest()->get(),
        };


        return response()->json([
            'branches' => $branches,
        ], 200);
    }

    //profile
    public function profile()
    {

        return view('pages.user.profile');
    }
    public function changePassword()
    {

        return view('pages.user.change-password');
    }

    public function updatePassword(Request $request)
    {

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        //if the current password is not the same as the one in db
        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->with('error', 'Your current password is incorrect!');
        }
        // Log out other devices
        Auth::logoutOtherDevices($request->current_password);

        User::find(auth()->user()->id)->update(['password' => Hash::make($request->password)]);


        return back()->with('message', 'Your password successfully changed!');
    }
}
