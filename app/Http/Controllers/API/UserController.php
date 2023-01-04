<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;


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
        $user->delete();
        return response('User Deleted Successfully');
    }

    public function updateProfile(Request $request, $user_id){

        $profile = User::find($user_id);

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $file_name = $file->getClientOriginalName();
            $file->move(public_path('/profile_image'), $file_name);
            $file_path =  URL::to('/') . '/profile_image/'.$file_name;
        } else {
            $file_path = null;
        }

        if($request->profile_image == null){
            $profile->profile_image = $profile->profile_image;
        }else{
            $profile->profile_image = $file_path;
        }

        $profile->save();

        return $profile;
    }
}
