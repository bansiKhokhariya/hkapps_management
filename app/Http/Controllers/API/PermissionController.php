<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index(){
        $permission = Permission::all();
        return $permission;
    }
    public function updatePermission(Request $request , $role_id){

        $this->validate($request, [
            'permission' => 'required',
        ]);

        $role = Role::find($role_id);

        $role->syncPermissions($request->permission);
        return response()->json(['message' => ' User Permission updated successfully', 'User' => $role]);

    }
}
