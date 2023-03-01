<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Role;


class UserController extends Controller
{
    public function index()
    {
        $user = User::get();
        return UserResource::collection($user);
    }

    public function store(CreateEmployeeRequest $request)
    {
        return UserResource::make($request->persist());
    }

    public function show(User $user)
    {
        return UserResource::make($user);
    }

    public function update(UpdateEmployeeRequest $request, User $user)
    {
        return UserResource::make($request->persist($user));
    }

    public function destroy(User $user)
    {

        // role delete //
        $convertRole = str_replace(' ', '_', strtolower($user->roles));
        $role = Role::findByName($convertRole, 'web');
        $role->delete();

        // user delete //
        $user->delete();

        return response('User Deleted Successfully');
    }

    public function updateProfile(Request $request, $user_id)
    {
        $profile = User::find($user_id);
        if ($request->name) {
            $convertOldRole = str_replace(' ', '_', strtolower($profile->name));
            $getRole = Role::where('name', $convertOldRole)->first();
            $convertRole = str_replace(' ', '_', strtolower($request->name));
            $updateRole = Role::find($getRole->id);
            $updateRole->name = $convertRole;
            $updateRole->save();
            $role = Role::findByName($convertRole, 'web');

            $profile->roles = $convertRole;
            $profile->assignRole($role);
        }
        if ($request->password) {
            $profile->password = bcrypt($request->password);
        }
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $file_name = $file->getClientOriginalName();
            $file->move(public_path('/profile_image'), $file_name);
            $file_path = URL::to('/') . '/profile_image/' . $file_name;
        } else {
            $file_path = null;
        }
        if ($request->profile_image == null) {
            $profile->profile_image = $profile->profile_image;
        } else {
            $profile->profile_image = $file_path;
        }
        if ($request->company_master_id) {
            $profile->company_master_id = $request->company_master_id;
        }
        if ($request->name) {
            $profile->name = $request->name;
        }
        if ($request->designation) {
            $profile->designation = $request->designation;
        }
        $profile->save();
        return $profile;
    }
}
