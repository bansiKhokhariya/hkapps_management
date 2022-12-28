<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index(){
        $setting = Setting::all();
        return $setting;
    }

    public function store(Request $request){

        $setting = Setting::find(1);

        if (!is_null($setting)){
            $setting->update([
                'time'=>$request->time,
            ]);
        }
        else{
            Setting::create([
                'time'=>$request->time,
            ]);
        }
        return $setting;
    }

    public function show(){

         $setting = Setting::find(1);
         return $setting;

    }


}
