<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use App\Events\RedisDataEvent;

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

    public function startAppDetailsUpdateCron()
    {

        \Log::info('start details cron!');
        $setting = Setting::where('cron', 'AppDetailsUpdate')->first();
        $setting->infinity = 1;
        $setting->save();

        for ($i = 1; $i <= INF; $i++) {
            $getSetting = Setting::where('cron', 'AppDetailsUpdate')->first();
            if ((int)$getSetting->infinity === 1) {
                sleep($getSetting->time * 60);
                \Artisan::call('AppDetailsUpdate:cron');
            } elseif ((int)$getSetting->infinity === 0) {
                break;
            }
        }

    }

    public function stopAppDetailsUpdateCron()
    {

        $setting = Setting::where('cron', 'AppDetailsUpdate')->first();
        $setting->infinity = 0;
        $setting->save();

    }

    public function startCheckAppStatusCron()
    {

        \Log::info('start check status cron!');
        execInBackground('php artisan CheckAppStatus:cron');

//        $setting = Setting::where('cron', 'CheckAppStatus')->first();
//        $setting->infinity = 1;
//        $setting->save();
//
//        for ($i = 1; $i <= INF; $i++) {
//            $getSetting = Setting::where('cron', 'CheckAppStatus')->first();
//            if ((int)$getSetting->infinity === 1) {
//                sleep($getSetting->time * 60);
//                \Artisan::call('CheckAppStatus:cron');
//            } elseif ((int)$getSetting->infinity === 0) {
//                break;
//            }
//        }
    }

    public function stopCheckAppStatusCron()
    {

        $setting = Setting::where('cron', 'CheckAppStatus')->first();
        $setting->infinity = 0;
        $setting->save();

    }

    public function startWebCreonCron()
    {
        \Log::info('start webcreraon cron!');
        execInBackground('php artisan WebCreon:cron');

//        $setting = Setting::where('cron', 'WebCreon')->first();
//        $setting->infinity = 1;
//        $setting->save();
//
//        for ($i = 1; $i <= INF; $i++) {
//            $getSetting = Setting::where('cron', 'WebCreon')->first();
//            if ((int)$getSetting->infinity === 1) {
//                sleep($getSetting->time * 60);
//                \Artisan::call('WebCreon:cron');
//            } elseif ((int)$getSetting->infinity === 0) {
//                break;
//            }
//        }
    }

    public function stopWebCreonCron()
    {

        $setting = Setting::where('cron', 'WebCreon')->first();
        $setting->infinity = 0;
        $setting->save();

    }

    public function startSpyAppCron()
    {

        $setting = Setting::where('cron', 'SpyApp')->first();
        $setting->infinity = 1;
        $setting->save();

        for ($i = 1; $i <= INF; $i++) {
            $getSetting = Setting::where('cron', 'SpyApp')->first();
            if ((int)$getSetting->infinity === 1) {
                sleep($getSetting->time * 60);
                \Artisan::call('SpyApp:cron');
            } elseif ((int)$getSetting->infinity === 0) {
                break;
            }
        }
    }

    public function stopSpyAppCron()
    {

        $setting = Setting::where('cron', 'SpyApp')->first();
        $setting->infinity = 0;
        $setting->save();

    }

    public function startSpyAppDetailsCron()
    {
        ini_set('max_execution_time', 0);

        $setting = Setting::where('cron', 'SpyAppDetails')->first();
        $setting->infinity = 1;
        $setting->save();


        for ($i = 1; $i <= INF; $i++) {
            $getSetting = Setting::where('cron', 'SpyAppDetails')->first();
            if ((int)$getSetting->infinity === 1) {
                sleep($getSetting->time * 60);
                \Artisan::call('SpyAppDetails:cron');
            } elseif ((int)$getSetting->infinity === 0) {
                break;
            }
        }
    }

    public function stopSpyAppDetailsCron()
    {

        $setting = Setting::where('cron', 'SpyAppDetails')->first();
        $setting->infinity = 0;

        $setting->save();

    }

    public function refreshCheckStatus (){

        \Artisan::call('CheckAppStatus:cron');

    }


}
