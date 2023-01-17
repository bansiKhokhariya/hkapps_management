<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::all();
        return $setting;
    }

    public function store(Request $request)
    {
        $setting = Setting::where('cron', $request->cron)->first();
        if (!is_null($setting)) {
            $setting->update([
                'time' => $request->time,
            ]);
        } else {
            $setting = Setting::create([
                'time' => $request->time,
                'cron' => $request->cron,
            ]);
        }
        return $setting;
    }

    public function show()
    {

        $setting = Setting::all();
        return $setting;
        //  $finalArray = [];
        // $CheckAppStatus = Setting::find(1);
        // $object1 = array('CheckAppStatus' => $CheckAppStatus->time);
        // $AppDetailsUpdate = Setting::find(2);
        // $object2 = array('AppDetailsUpdate' => $AppDetailsUpdate->time);
        // $finalArray = array_merge($finalArray,$object1,$object2);
        // return $finalArray;

    }

    public function startAppdetailsUpdateCron()
    {

        \Log::info("start AppdetailsUpdate cron");
        \Artisan::call('AppDetailsUpdate:cron');
        \Log::info("end AppdetailsUpdate cron");
    }

    public function startCheckAppStatusCron()
    {

        \Log::info("start CheckAppStatus cron");
        \Artisan::call('CheckAppStatus:cron');
        \Log::info("end CheckAppStatus cron");
    }


}
