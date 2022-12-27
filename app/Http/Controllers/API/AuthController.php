<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $user = Auth::user();
            $users = User::where('id',Auth::user()->id)->with('roles.permissions')->get();
            $success['token'] = $user->createToken('login_token')->accessToken;
            $success['details'] = $users;

            $activity = new Activity();
            $activity->log_name = 'User';
            $activity->description = 'login';
            $activity->causer_type = 'App\Models\User';
            $activity->causer_id  = Auth::user()->id;
            $activity->save();

            return response()->json(['message' => 'User login successfully.', 'user' => $success]);
        } else {
            return response()->json(['message' => 'These Credentials do not match our records !'])->setStatusCode(401);
        }
    }
    public function logout(Request $request)
    {

        auth()->user()->tokens()->delete();

        $activity = new Activity();
        $activity->log_name = 'User';
        $activity->description = 'logout';
        $activity->causer_type = 'App\Models\User';
        $activity->causer_id  = Auth::user()->id;
        $activity->save();

        return [
            'message' => 'You have successfully logged out and the token was successfully deleted'
        ];

    }

}
