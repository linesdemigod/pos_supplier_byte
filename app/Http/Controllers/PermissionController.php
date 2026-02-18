<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function allPermission()
    {


        $permissions = Permission::all();

        return view('pages.admin.permission.index', compact('permissions'));
    }

    public function addPermission()
    {
        return view('pages.admin.permission.add');
    }

    public function storePermission(Request $request)
    {
        $formData = $request->validate([
            'name' => 'required',
            'group_name' => 'required|not_in:0',

        ]);

        Permission::create($formData);

        return response()->json(['success' => 'Permission Added']);
    }

    public function editPermission(Permission $permission)
    {
        return view('pages.admin.permission.edit-permission', compact('permission'));
    }

    public function updatePermission(Request $request, Permission $permission)
    {
        $formData = $request->validate([
            'name' => 'required',
            'group_name' => 'required|not_in:0',

        ]);

        $permission->update($formData);

        return redirect()->route('all.permission')->with('message', 'Permission updated successfully');
    }

    //delete account
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('all.permission')->with('message', 'Permission deleted successfully');
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function allRole()
    {
        $roles = Role::all();

        return view('pages.admin.role.index', compact('roles'));
    }

    public function addRole()
    {
        return view('pages.admin.role.add');
    }

    public function storeRole(Request $request)
    {
        $formData = $request->validate([
            'name' => 'required',

        ]);

        Role::create($formData);

        return response()->json(['success' => 'RoleAdded']);
    }

    public function editRole(Role $role)
    {
        return view('pages.admin.role.edit-role', compact('role'));
    }

    public function updateRole(Request $request, Role $role)
    {
        $formData = $request->validate([
            'name' => 'required',

        ]);

        $role->update($formData);

        return redirect()->route('all.role')->with('message', 'Role updated successfully');
    }

    //delete account
    public function destroyRole(Role $role)
    {
        $role->delete();

        return redirect()->route('permission.all.role')->with('message', 'Role deleted successfully');
    }

    /////////////////////////// role permission /////////////////////////////////////////

    public function addRolesPermission()
    {
        $permissions = Permission::all();
        $roles = Role::all();
        $permission_groups = User::getPermission();

        // dd($permission_groups);

        return view('pages.admin.role.add-roles-permission', compact('permissions', 'roles', 'permission_groups'));

    }

    public function rolePermissionStore(Request $request)
    {
        $role_id = $request->role_id;
        $permissions = $request->permission;

        // Loop through each permission
        foreach ($permissions as $permission_id) {
            // Check if the record already exists
            $existingPermission = DB::table('role_has_permissions')
                ->where('role_id', $role_id)
                ->where('permission_id', $permission_id)
                ->first();

            if ($existingPermission) {
                // If it exists, update the record
                DB::table('role_has_permissions')
                    ->where('role_id', $role_id)
                    ->where('permission_id', $permission_id)
                    ->update(['role_id' => $role_id, 'permission_id' => $permission_id]);
            } else {
                // If it doesn't exist, insert the new record
                DB::table('role_has_permissions')->insert([
                    'role_id' => $role_id,
                    'permission_id' => $permission_id,
                ]);
            }
        }

        return redirect()->route('permission.all.role.permission')->with('message', 'Permission added successfully');
    }

    public function allRolesPermission()
    {
        $roles = Role::with('permissions')->get();

        return view('pages.admin.role.all-roles-permission', compact('roles'));
    }

    public function adminRolesEdit(Role $role)
    {

        $permissions = Permission::all();
        $permission_groups = User::getPermission();



        return view('pages.admin.role.edit-role-permission', compact('role', 'permissions', 'permission_groups'));
    }

    public function AdminRolesUpdate(Request $request, Role $role)
    {
        $permission = $request->permission;

        if (!empty($permission)) {
            $role->syncPermissions($permission);
        }

        return redirect()->route('permission.all.role.permission')->with('message', 'Role Permission Updated successfully');
    }

    //delete account
    public function destroyRoleAdmin(Role $role)
    {
        if (!is_null($role)) {
            $role->delete();
        }

        return redirect()->back()->with('message', 'Role deleted successfully');
    }
}
