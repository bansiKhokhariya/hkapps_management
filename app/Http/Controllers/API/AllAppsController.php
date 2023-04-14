<?php

namespace App\Http\Controllers\API;

use App\Events\UserEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAllAppRequest;
use App\Http\Requests\CreateTestAllAppsRequest;
use App\Http\Requests\UpdateAllAppRequest;
use App\Http\Requests\UpdateTestAllAppRequest;
use App\Http\Resources\AllAppResource;
use App\Http\Resources\TestAllAppResource;
use App\Models\AdPlacement;
use App\Models\AllApps;
use App\Models\ApikeyList;
use App\Models\AppDetails;
use App\Models\TestAdPlacement;
use App\Models\TestAllApp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\RedisDataEvent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use App\Models\GitHubToken;
use App\Models\AppRelatedWord;

class AllAppsController extends Controller
{

    public function index()
    {
        // $companyUser = Auth::user()->company_master_id;
        // if (!$companyUser) {
        //     $allApp = AllApps::where('status', 'live')->latest()->get();
        // } else {
        //     $allApp = AllApps::where('company_master_id', $companyUser)->where('status', 'live')->latest()->get();
        // }
        // return AllAppResource::collection($allApp);


        $redis3 = Redis::connection('RedisApp3');
        $response3 = $redis3->keys("*");
        $emptyArray = [];

        $collection = collect($response3);

        $allApps = AllApps::whereIn('app_packageName',$response3)->pluck('app_packageName');

        $diff = $collection->diffKeys($allApps);
        $rr = $diff->all();

        $map = array_map(function($value){
            return  (object)[
                "app_packageName" => $value,
            ];
        },$rr);

        $tt = array_values($map);

        $allApp = AllApps::whereIn('app_packageName',$response3)->where('status', 'live')->latest()->get()->toArray();

        $temp =   array_merge((array)$tt,$allApp);

        $final =   array_values($temp);
        return $final;
//   return  AllAppResource::collection($final);



    }

    public function store(CreateAllAppRequest $request)
    {

        $app_details_link = "https://play.google.com/store/apps/details?id=" . $request->app_packageName;
        $res = Http::get($app_details_link);
        if ($res->status() == 200) {
            return response()->json([
                'message' => 'This package name already exists in playStore!',
            ], 422);
        } else {
            $allApps = AllAppResource::make($request->persist());
        }
        return $allApps;
    }

    public function show($package_name)
    {

        $allApp = AllApps::where('app_packageName',$package_name)->first();
        $redis = Redis::connection('RedisApp2');
        $response = $redis->get($package_name);
        $db_response = json_decode($response);
        if ($db_response) {

            $dd = $db_response->APP_SETTINGS;

            // **** app parameter **** //
            $app_parameter = [];
            foreach ($dd as $key => $value) {
                $contains = str_starts_with($key, 'app_');
                if (!$contains) {
                    $hint = json_encode(array($key => $value));
                    $rewardedVideo_object = array('name' => $key, 'value' => $value, 'hint' => $hint);
                    array_push($app_parameter, $rewardedVideo_object);
                }
            }

            $app_parameter_array = ['app_parameter' => $app_parameter];
            $dd = array_merge((array)$dd, $app_parameter_array);
            // **** //


            // **** app setting **** //
            $app_setting = [];
            foreach ($dd as $key => $value) {
                $contains = str_starts_with($key, 'app_');
                if ($contains) {
                    $rewardedVideo_object = array($key => $value);
                    $app_setting = array_merge((array)$app_setting, $rewardedVideo_object);
                }
            }
            // **** //


            // **** app extra **** //
            $app_extra = $db_response->EXTRA_DATA;
            $extra_obj = array('app_extra' => $app_extra);
            $app_setting = array_merge($app_setting, $extra_obj);
            // **** //


            // **** monetize setting **** //

            $placement = $db_response->PLACEMENT;
            $monetize_setting = [];

            foreach ($placement as $key => $value) {

                $platform_adFormat = ['App ID' , 'Banner' , 'Interstitial' , 'Native' , 'Rewarded Video' ,'Native Banner','App Open'];
                if (isset($value->AppID) && $value->AppID != '') {
                    $ad_Appid = $value->AppID;
                    // array_push($platform_adFormat, 'App ID');
                }else {
                    $ad_Appid = '';
                }


                $ad_Banner = [];
                $ad_Interstitial = [];
                $ad_Native = [];
                $ad_NativeBanner = [];
                $ad_RewardedVideo = [];
                $ad_RewardedInterstitial = [];
                $ad_AppOpen = [];

                foreach ($value as $item => $i) {
                    if (str_starts_with($item, 'Banner')) {
                        array_push($ad_Banner, $i);
                        // array_push($platform_adFormat, 'Banner');
                    }
                    if (str_starts_with($item, 'Interstitial')) {
                        array_push($ad_Interstitial, $i);
                        // array_push($platform_adFormat, 'Interstitial');
                    }
                    if (str_starts_with($item, 'Native') && !str_starts_with($item, 'NativeBanner')) {
                        array_push($ad_Native, $i);
                        // array_push($platform_adFormat, 'Native');
                    }
                    if (str_starts_with($item, 'NativeBanner')) {
                        array_push($ad_NativeBanner, $i);
                        // array_push($platform_adFormat, 'Native Banner');
                    }
                    if (str_starts_with($item, 'RewardedVideo')) {
                        array_push($ad_RewardedVideo, $i);
                        // array_push($platform_adFormat, 'Rewarded Video');
                    }
                    if (str_starts_with($item, 'RewardedInterstitial')) {
                        array_push($ad_RewardedInterstitial, $i);
                        // array_push($platform_adFormat, 'Rewarded Interstitial');
                    }
                    if (str_starts_with($item, 'AppOpen')) {
                        array_push($ad_AppOpen, $i);
                        // array_push($platform_adFormat, 'App Open');
                    }
                }


                $monetize_setting_object = array('platform_name' => $key, 'platform_adFormat' => $platform_adFormat, 'ad_AppID' => $ad_Appid, 'ad_Banner' => $ad_Banner, 'ad_Interstitial'
                => $ad_Interstitial, 'ad_Native' => $ad_Native, 'ad_NativeBanner' => $ad_NativeBanner, 'ad_RewardedVideo' => $ad_RewardedVideo
                , 'ad_RewardedInterstitial' => $ad_RewardedInterstitial, 'ad_AppOpen' => $ad_AppOpen, 'ad_loadAdIdsType' => $value->ad_loadAdIdsType, 'ad_showAdStatus' => $value->ad_showAdStatus);
                array_push($monetize_setting, $monetize_setting_object);

            }
            $monetize_array = ['monetize_setting' => $monetize_setting];
            $app_setting = array_merge((array)$app_setting, $monetize_array);

            // **** //


            // **** status **** //
            $status = $db_response->STATUS;
            $status_obj = array('STATUS' => $status);
            $app_setting = array_merge($app_setting, $status_obj);
            // **** //

            // **** MSG **** //
            $msg = $db_response->MSG;
            $msg_obj = array('MSG' => $msg);
            $app_setting = array_merge($app_setting, $msg_obj);
            // **** //

            // **** Advertise_List **** //
            $Advertise_List = $db_response->Advertise_List;
            $Advertise_List_obj = array('Advertise_List' => $Advertise_List);
            $app_setting = array_merge($app_setting, $Advertise_List_obj);
            // **** //

            // **** MORE_APP_SPLASH **** //
            $more_app_splash = $db_response->MORE_APP_SPLASH;
            $more_app_splash_obj = array('MORE_APP_SPLASH' => $more_app_splash);
            $app_setting = array_merge($app_setting, $more_app_splash_obj);
            // **** //

            // **** MORE_APP_EXIT **** //
            $more_app_exit = $db_response->MORE_APP_EXIT;
            $more_app_exit_obj = array('MORE_APP_EXIT' => $more_app_exit);
            $app_setting = array_merge($app_setting, $more_app_exit_obj);
            // **** //

            $app_details = AllAppResource::make($allApp);

            return response()->json(['data' => $app_setting , 'app_details'=> $app_details]);

        } else {
            return response()->json(['message' => 'This app Redis 6 data is null!'], 404);
        }
        // return AllAppResource::make($allApp);
    }

    public function testShow(TestAllApp $testAllApp)
    {
        return TestAllAppResource::make($testAllApp);
    }

    public function update(Request $request ,$package_name)
    {

//        $package_name = $allApp->package_name;
        $db2_response = json_decode($request->db2_response);



        // sql db save //

        $allApp = AllApps::where('app_packageName',$package_name)->first();

        $AllApps = AllApps::find($allApp->id);

        if ($db2_response->app_updateAppDialogStatus == null) {
            $AllApps->app_updateAppDialogStatus = $AllApps->app_updateAppDialogStatus;
        } else {
            if (json_decode($db2_response->app_updateAppDialogStatus)) {
                $AllApps->app_updateAppDialogStatus = 1;
            } else {
                $AllApps->app_updateAppDialogStatus = 0;
            }
        }
        $AllApps->app_versionCode = $db2_response->app_versionCode;
        if ($db2_response->app_redirectOtherAppStatus == null) {
            $AllApps->app_redirectOtherAppStatus = $AllApps->app_redirectOtherAppStatus;
        } else {
            if (json_decode($db2_response->app_redirectOtherAppStatus)) {
                $AllApps->app_redirectOtherAppStatus = 1;
            } else {
                $AllApps->app_redirectOtherAppStatus = 0;
            }
        }
        $AllApps->app_newPackageName = $db2_response->app_newPackageName;
        $AllApps->app_privacyPolicyLink = $db2_response->app_privacyPolicyLink;
        // $AllApps->app_accountLink = $db2_response->app_accountLink;
        if ($db2_response->app_adShowStatus == null) {
            $AllApps->app_adShowStatus = $AllApps->app_adShowStatus;
        } else {
            if (json_decode($db2_response->app_adShowStatus)) {
                $AllApps->app_adShowStatus = 1;
            } else {
                $AllApps->app_adShowStatus = 0;
            }
        }
        if ($db2_response->app_AppOpenAdStatus == null) {
            $AllApps->app_AppOpenAdStatus = $AllApps->app_AppOpenAdStatus;
        } else {
            if (json_decode($db2_response->app_AppOpenAdStatus)) {
                $AllApps->app_AppOpenAdStatus = 1;
            } else {
                $AllApps->app_AppOpenAdStatus = 0;
            }
        }
        $AllApps->app_howShowAd = $db2_response->app_howShowAd;
        $AllApps->app_adPlatformSequence = $db2_response->app_adPlatformSequence;
        $AllApps->app_alternateAdShow = $db2_response->app_alernateAdShow;
        if (isset($db2_response->app_testAdStatus)) {
            $AllApps->app_testAdStatus = $db2_response->app_testAdStatus;
        }

        $AllApps->app_mainClickCntSwAd = $db2_response->app_mainClickCntSwAd;
        $AllApps->app_innerClickCntSwAd = $db2_response->app_innerClickCntSwAd;
        $AllApps->app_parameter = $db2_response->app_parameter;
        $AllApps->save();

        //  **********  //


        // ********************* db 2 response save ********************* //

        // **** app setting **** //
        $app_settings = [];
        foreach ($db2_response as $key => $value) {
            $contains = str_starts_with($key, 'app_');
            if ($contains) {
                $rewardedVideo_object = array($key => $value);
                $app_settings = array_merge((array)$app_settings, $rewardedVideo_object);
            }
        }
        // ****** //

        // *****  app parameter ***** //
        $app_para_decode = $db2_response->app_parameter;
        if ($app_para_decode) {
            $app_parameter_array = [];
            foreach ($app_para_decode as $key => $value) {
                $object = array($value->name => $value->value);
                $app_parameter_array = array_merge($app_parameter_array, $object);
            }
        } else {
            $app_parameter_array = [];
        }
        $app_settings = array_merge($app_settings, $app_parameter_array);
        // ****** //


        // ***** remove field ***** //
        unset($app_settings['app_parameter']);
        unset($app_settings['app_extra']);
        // **** //


        // ****** placement ****** //
        $get_placement = $db2_response->monetize_setting;
        $placement_array = [];
        foreach ($get_placement as $key => $value) {

            $new_placement = json_decode(json_encode($value), true);
            // for banner //
            $banner_array = array();
            if (isset($value->ad_Banner)) {
                $decode_value = $value->ad_Banner;
                foreach ($decode_value as $key => $banner) {
                    $plus = $key + 1;
                    $banner_object = array("Banner{$plus}" => $banner);
                    $banner_array = array_merge($banner_array, $banner_object);
                }
            }

            // for ad_Interstitial //
            $interstitial_array = array();
            if (isset($value->ad_Interstitial)) {
                $decode_value = $value->ad_Interstitial;
                foreach ($decode_value as $key => $interstitial) {
                    $plus = $key + 1;
                    $interstitial_object = array("Interstitial{$plus}" => $interstitial);
                    $interstitial_array = array_merge($interstitial_array, $interstitial_object);
                }
            }


            // for native //
            $native_array = array();
            if (isset($value->ad_Native)) {
                $decode_value = $value->ad_Native;
                foreach ($decode_value as $key => $native) {
                    $plus = $key + 1;
                    $native_object = array("Native{$plus}" => $native);
                    $native_array = array_merge($native_array, $native_object);
                }
            }


            // for ad_NativeBanner //
            $nativeBanner_array = array();
            if (isset($value->ad_NativeBanner)) {
                $decode_value = $value->ad_NativeBanner;
                foreach ($decode_value as $key => $native_banner) {
                    $plus = $key + 1;
                    $native_banner_object = array("NativeBanner{$plus}" => $native_banner);
                    $nativeBanner_array = array_merge($nativeBanner_array, $native_banner_object);
                }
            }


            // for ad_RewardedVideo //
            $rewardedVideo_array = array();
            if (isset($value->ad_RewardedVideo)) {
                $decode_value = $value->ad_RewardedVideo;
                foreach ($decode_value as $key => $rewardedVideo) {
                    $plus = $key + 1;
                    $rewardedVideo_object = array("RewardedVideo{$plus}" => $rewardedVideo);
                    $rewardedVideo_array = array_merge($rewardedVideo_array, $rewardedVideo_object);
                }
            }


            // for ad_RewardedInterstitial //
            $rewardedInterstitial_array = array();
            if (isset($value->ad_RewardedInterstitial)) {
                $decode_value = $value->ad_RewardedInterstitial;
                foreach ($decode_value as $key => $rewardedInterstitial) {
                    $plus = $key + 1;
                    $rewardedInterstitial_object = array("RewardedInterstitial{$plus}" => $rewardedInterstitial);
                    $rewardedInterstitial_array = array_merge($rewardedInterstitial_array, $rewardedInterstitial_object);
                }
            }

            // for ad_AppOpen //
            $appOpen_array = array();
            if (isset($value->ad_AppOpen)) {
                $decode_value = $value->ad_AppOpen;
                foreach ($decode_value as $key => $appOpen) {
                    $plus = $key + 1;
                    $appOpen_object = array("AppOpen{$plus}" => $appOpen);
                    $appOpen_array = array_merge($appOpen_array, $appOpen_object);
                }
            }


            $allAdformat = array_merge($banner_array, $interstitial_array, $native_array, $nativeBanner_array, $rewardedVideo_array, $rewardedInterstitial_array, $appOpen_array);
            $placement = array_merge($new_placement, $allAdformat);


            array_push($placement_array, $placement);
        }



        $new_placement_array = [];
        foreach ($placement_array as $key => $value) {
            if(isset($value['ad_AppID']) && $value['ad_AppID'] == ''){
                unset($value['ad_AppID']);
            }
            unset($value['ad_Banner']);
            unset($value['ad_Interstitial']);
            unset($value['ad_Native']);
            unset($value['ad_NativeBanner']);
            unset($value['ad_RewardedVideo']);
            unset($value['ad_RewardedInterstitial']);
            unset($value['ad_AppOpen']);
            unset($value['platform_adFormat']);
            $covert_obj = (object)$value;
            unset($value['platform_name']);
            $platformName = str_replace(' ', '', $covert_obj->platform_name);
            $object1 = array($platformName => $value);
            $new_placement_array = array_merge($new_placement_array, $object1);
        }


        $response = (object)["STATUS" => $db2_response->STATUS, "MSG" => $db2_response->MSG, "APP_SETTINGS" => $app_settings, "PLACEMENT" => $new_placement_array,
            "Advertise_List" => $db2_response->Advertise_List, "MORE_APP_SPLASH" => $db2_response->MORE_APP_SPLASH, "MORE_APP_EXIT" => $db2_response->MORE_APP_EXIT, "EXTRA_DATA" => $db2_response->app_extra];

        return $response;

        $redis = Redis::connection('RedisApp2');
        $redis->set($package_name, json_encode($response));

        // ****************** //


        return 'data set succesfully!';



//        return AllAppResource::make($request->persist($allApp));
    }

    public function destroy(AllApps $allApp)
    {
        $id = Auth::user()->id;
        $auth_user = User::find($id);
        $allApp->delete();

        // call event
        // event(new UserEvent($auth_user));
        return response('App Deleted Successfully');
    }

    public function store_monetize(Request $request)
    {

        $get_adplacement = AdPlacement::where('allApps_id', $request->allApps_id)->where('platform_name', $request->platform_name)->first();
        if ($get_adplacement) {
            $ad_placement = AdPlacement::find($get_adplacement->id);
            $ad_placement->allApps_id = $request->allApps_id;
            $ad_placement->platform_name = $request->platform_name;
            $ad_placement->ad_loadAdIdsType = $request->ad_loadAdIdsType;
            $ad_placement->ad_AppID = $request->ad_AppID;
            $ad_placement->ad_Banner = $request->ad_Banner;
            $ad_placement->ad_Interstitial = $request->ad_Interstitial;
            $ad_placement->ad_Native = $request->ad_Native;
            $ad_placement->ad_NativeBanner = $request->ad_NativeBanner;
            $ad_placement->ad_RewardedVideo = $request->ad_RewardedVideo;
            $ad_placement->ad_AppOpen = $request->ad_AppOpen;
            $ad_placement->save();
        } else {
            $ad_placement = new AdPlacement();
            $ad_placement->allApps_id = $request->allApps_id;
            $ad_placement->platform_name = $request->platform_name;
            $ad_placement->ad_loadAdIdsType = $request->ad_loadAdIdsType;
            $ad_placement->ad_AppID = $request->ad_AppID;
            $ad_placement->ad_Banner = json_encode($request->ad_Banner);
            $ad_placement->ad_Interstitial = json_encode($request->ad_Interstitial);
            $ad_placement->ad_Native = json_encode($request->ad_Native);
            $ad_placement->ad_NativeBanner = json_encode($request->ad_NativeBanner);
            $ad_placement->ad_RewardedVideo = json_encode($request->ad_RewardedVideo);
            $ad_placement->ad_AppOpen = json_encode($request->ad_AppOpen);
            $ad_placement->save();
        }

        return $ad_placement;

    }

    public function viewAppRes(Request $request)
    {

        $package_name = $request->packageName;
        $api_key = $request->apikey;
        $redis2 = Redis::connection('RedisApp2');
        $response = $redis2->get($package_name);

        // $res_obj = json_decode($response);

        // $allApps = AllApps::Where('app_packageName', $package_name)->first();

//         if ($res_obj) {
//             $redis3 = Redis::connection('RedisApp3');
//             $response3 = $redis3->get($package_name);
//             $response3Convert = str_replace(["'"], '"', $response3);

//             $meta_keywords = json_decode($response3Convert)->AFHJNTGDGD563200K;

// //            $meta_keywords = explode(',', $res_obj->APP_SETTINGS->app_apikey);
//             if ($api_key == $meta_keywords) {
//                 $get_api_key = ApikeyList::where('apikey_packageName', $package_name)->where('apikey_text', $api_key)->first();
//                 if ($get_api_key) {
//                     $apikey_request_count = $get_api_key->apikey_request;
//                     $apiKey = ApikeyList::find($get_api_key->id);
//                     $apiKey->apikey_request = $apikey_request_count + 1;
//                     $apiKey->save();
//                 } else {
//                     $apiKey = new ApikeyList();
//                     $apiKey->apikey_packageName = $package_name;
//                     $apiKey->apikey_text = $api_key;
//                     if ($allApps) {
//                         $apiKey->is_available = 1;
//                     } else {
//                         $apiKey->is_available = 0;
//                     }
//                     $apiKey->save();
//                 }
//             } else {
//                 $apiKey = new ApikeyList();
//                 $apiKey->apikey_packageName = $package_name;
//                 $apiKey->apikey_text = $api_key;
//                 if ($allApps) {
//                     $apiKey->is_available = 1;
//                 } else {
//                     $apiKey->is_available = 0;
//                 }
//                 $apiKey->save();
//             }
//         } else {
//             $apiKey = new ApikeyList();
//             $apiKey->apikey_packageName = $package_name;
//             $apiKey->apikey_text = $api_key;
//             if ($allApps) {
//                 $apiKey->is_available = 1;
//             } else {
//                 $apiKey->is_available = 0;
//             }
//             $apiKey->save();
//         }


        // call event
        // event(new RedisDataEvent());


        return $response;
    }

    public function appRestore($id)
    {

        $id = Auth::user()->id;
        $auth_user = User::find($id);

        AllApps::withTrashed()->find($id)->restore();
        // call event
        // event(new UserEvent($auth_user));
        return response('App Restore Successfully');

    }

    public function updatePrivacypolicyLink($id, Request $request)
    {
        $allApps = AllApps::find($id);

        if (!is_null($allApps)) {
            $allApps->update([
                'app_privacyPolicyLink' => $request->app_privacyPolicyLink,
            ]);
        }

        return response('privacy policy link updated succesfully!');
    }

    public function searchPackage($package_name)
    {

        $allApp = AllApps::where('app_packageName', 'Like', '%' . $package_name . '%')->get();
        return AllAppResource::collection($allApp);

    }

    public function storePackage()
    {
        $redis2 = Redis::connection('RedisApp2');
        $keys = $redis2->keys('*');

        $allApps = AllApps::pluck('app_packageName')->toArray();
        $result = array_diff($keys, $allApps);

        $redis = Redis::connection('RedisApp10');

        foreach ($keys as $key) {

//            $app_details_link = "https://play.google.com/store/apps/details?id=" . $key;
//            $res = Http::get($app_details_link);
//            if ($res->status() === 200) {

            $get_App = AllApps::where('app_packageName', $key)->first();
            if (!$get_App) {
                $allApps = new AllApps();
                $allApps->app_packageName = $key;
                $allApps->app_apikey = $this->generateApikey();
                // $allApps->status = 'live';
                $allApps->save();


                // create github repo //
                // $getToken = GitHubToken::find(1);
                // $response = Http::withHeaders([
                //     'Authorization' => 'Bearer ' . $getToken->github_access_token,
                // ])->post('https://api.github.com/user/repos', [
                //     'name' => $allApps->app_packageName . '_' . $allApps->id,
                //     'description' => 'hkApps repo',
                // ]);
                // ************* //

            }

            // ***************** view app response json ******************** //
            $getApp = new AllApps();
            $result = $getApp->viewResponse($key, $this->generateApikey());
            $redis->set($key, json_encode($result));
            //**********//
//            }


        }


        return 'package add succesfully!';
    }

    public function generateApikey()
    {

        $bytes = random_bytes(12);
        return bin2hex($bytes);

    }

    public function getTestData($package_name)
    {

        $redis = Redis::connection('RedisApp6');
        $response = $redis->get($package_name);
        $db_response = json_decode($response);
        if ($db_response) {


            $dd = $db_response->APP_SETTINGS;

            // **** app parameter **** //
            $app_parameter = [];
            foreach ($dd as $key => $value) {
                $contains = str_starts_with($key, 'app_');
                if (!$contains) {
                    $hint = json_encode(array($key => $value));
                    $rewardedVideo_object = array('name' => $key, 'value' => $value, 'hint' => $hint);
                    array_push($app_parameter, $rewardedVideo_object);
                }
            }

            $app_parameter_array = ['app_parameter' => $app_parameter];
            $dd = array_merge((array)$dd, $app_parameter_array);
            // **** //


            // **** app setting **** //
            $app_setting = [];
            foreach ($dd as $key => $value) {
                $contains = str_starts_with($key, 'app_');
                if ($contains) {
                    $rewardedVideo_object = array($key => $value);
                    $app_setting = array_merge((array)$app_setting, $rewardedVideo_object);
                }
            }
            // **** //


            // **** app extra **** //
            $app_extra = $db_response->EXTRA_DATA;
            $extra_obj = array('app_extra' => $app_extra);
            $app_setting = array_merge($app_setting, $extra_obj);
            // **** //


            // **** monetize setting **** //

            $placement = $db_response->PLACEMENT;
            $monetize_setting = [];

            foreach ($placement as $key => $value) {
                $platform_adFormat = ['App ID' , 'Banner' , 'Interstitial' , 'Native' , 'Rewarded Video' ,'Native Banner' ,'App Open'];
                if (isset($value->AppID) && $value->AppID != '') {
                    $ad_Appid = $value->AppID;
                    // array_push($platform_adFormat, 'App ID');
                } else {
                    $ad_Appid = '';
                }


                $ad_Banner = [];
                $ad_Interstitial = [];
                $ad_Native = [];
                $ad_NativeBanner = [];
                $ad_RewardedVideo = [];
                $ad_RewardedInterstitial = [];
                $ad_AppOpen = [];

                foreach ($value as $item => $i) {
                    if (str_starts_with($item, 'Banner')) {
                        array_push($ad_Banner, $i);
                        // array_push($platform_adFormat, 'Banner');
                    }
                    if (str_starts_with($item, 'Interstitial')) {
                        array_push($ad_Interstitial, $i);
                        // array_push($platform_adFormat, 'Interstitial');
                    }
                    if (str_starts_with($item, 'Native')) {
                        array_push($ad_Native, $i);
                        // array_push($platform_adFormat, 'Native');
                    }
                    if (str_starts_with($item, 'NativeBanner')) {
                        array_push($ad_NativeBanner, $i);
                        // array_push($platform_adFormat, 'Native Banner');
                    }
                    if (str_starts_with($item, 'RewardedVideo')) {
                        array_push($ad_RewardedVideo, $i);
                        // array_push($platform_adFormat, 'Rewarded Video');
                    }
                    if (str_starts_with($item, 'RewardedInterstitial')) {
                        array_push($ad_RewardedInterstitial, $i);
                        // array_push($platform_adFormat, 'Rewarded Interstitial');
                    }
                    if (str_starts_with($item, 'AppOpen')) {
                        array_push($ad_AppOpen, $i);
                        // array_push($platform_adFormat, 'App Open');
                    }
                }


                $monetize_setting_object = array('platform_name' => $key, 'platform_adFormat' => $platform_adFormat, 'ad_AppID' => $ad_Appid, 'ad_Banner' => $ad_Banner, 'ad_Interstitial'
                => $ad_Interstitial, 'ad_Native' => $ad_Native, 'ad_NativeBanner' => $ad_NativeBanner, 'ad_RewardedVideo' => $ad_RewardedVideo
                , 'ad_RewardedInterstitial' => $ad_RewardedInterstitial, 'ad_AppOpen' => $ad_AppOpen, 'ad_loadAdIdsType' => $value->ad_loadAdIdsType, 'ad_showAdStatus' => $value->ad_showAdStatus);
                array_push($monetize_setting, $monetize_setting_object);

            }
            $monetize_array = ['monetize_setting' => $monetize_setting];
            $app_setting = array_merge((array)$app_setting, $monetize_array);

            // **** //


            // **** status **** //
            $status = $db_response->STATUS;
            $status_obj = array('STATUS' => $status);
            $app_setting = array_merge($app_setting, $status_obj);
            // **** //

            // **** MSG **** //
            $msg = $db_response->MSG;
            $msg_obj = array('MSG' => $msg);
            $app_setting = array_merge($app_setting, $msg_obj);
            // **** //

            // **** Advertise_List **** //
            $Advertise_List = $db_response->Advertise_List;
            $Advertise_List_obj = array('Advertise_List' => $Advertise_List);
            $app_setting = array_merge($app_setting, $Advertise_List_obj);
            // **** //

            // **** MORE_APP_SPLASH **** //
            $more_app_splash = $db_response->MORE_APP_SPLASH;
            $more_app_splash_obj = array('MORE_APP_SPLASH' => $more_app_splash);
            $app_setting = array_merge($app_setting, $more_app_splash_obj);
            // **** //

            // **** MORE_APP_EXIT **** //
            $more_app_exit = $db_response->MORE_APP_EXIT;
            $more_app_exit_obj = array('MORE_APP_EXIT' => $more_app_exit);
            $app_setting = array_merge($app_setting, $more_app_exit_obj);
            // **** //

            return response()->json(['data' => $app_setting]);

        } else {
            return response()->json(['message' => 'This app Redis 6 data is null!'], 404);
        }

    }

    public function setTestData(Request $request)
    {

        $package_name = $request->package_name;
        $test_response = json_decode($request->test_response);


        // **** app setting **** //
        $app_settings = [];
        foreach ($test_response as $key => $value) {
            $contains = str_starts_with($key, 'app_');
            if ($contains) {
                $rewardedVideo_object = array($key => $value);
                $app_settings = array_merge((array)$app_settings, $rewardedVideo_object);
            }
        }
        // ****** //

        // *****  app parameter ***** //
        $app_para_decode = $test_response->app_parameter;
        if ($app_para_decode) {
            $app_parameter_array = [];
            foreach ($app_para_decode as $key => $value) {
                $object = array($value->name => $value->value);
                $app_parameter_array = array_merge($app_parameter_array, $object);
            }
        } else {
            $app_parameter_array = [];
        }
        $app_settings = array_merge($app_settings, $app_parameter_array);

        // ****** //


        // ***** remove field ***** //
        unset($app_settings['app_parameter']);
        unset($app_settings['app_extra']);
        // **** //


        // ****** placement ****** //
        $get_placement = $test_response->monetize_setting;
        $placement_array = [];
        foreach ($get_placement as $key => $value) {

            $new_placement = json_decode(json_encode($value), true);
            // for banner //
            $banner_array = array();
            if (isset($value->ad_Banner)) {
                $decode_value = $value->ad_Banner;
                foreach ($decode_value as $key => $banner) {
                    $plus = $key + 1;
                    $banner_object = array("Banner{$plus}" => $banner);
                    $banner_array = array_merge($banner_array, $banner_object);
                }
            }

            // for ad_Interstitial //
            $interstitial_array = array();
            if (isset($value->ad_Interstitial)) {
                $decode_value = $value->ad_Interstitial;
                foreach ($decode_value as $key => $interstitial) {
                    $plus = $key + 1;
                    $interstitial_object = array("Interstitial{$plus}" => $interstitial);
                    $interstitial_array = array_merge($interstitial_array, $interstitial_object);
                }
            }


            // for native //
            $native_array = array();
            if (isset($value->ad_Native)) {
                $decode_value = $value->ad_Native;
                foreach ($decode_value as $key => $native) {
                    $plus = $key + 1;
                    $native_object = array("Native{$plus}" => $native);
                    $native_array = array_merge($native_array, $native_object);
                }
            }


            // for ad_NativeBanner //
            $nativeBanner_array = array();
            if (isset($value->ad_NativeBanner)) {
                $decode_value = $value->ad_NativeBanner;
                foreach ($decode_value as $key => $native_banner) {
                    $plus = $key + 1;
                    $native_banner_object = array("NativeBanner{$plus}" => $native_banner);
                    $nativeBanner_array = array_merge($nativeBanner_array, $native_banner_object);
                }
            }


            // for ad_RewardedVideo //
            $rewardedVideo_array = array();
            if (isset($value->ad_RewardedVideo)) {
                $decode_value = $value->ad_RewardedVideo;
                foreach ($decode_value as $key => $rewardedVideo) {
                    $plus = $key + 1;
                    $rewardedVideo_object = array("RewardedVideo{$plus}" => $rewardedVideo);
                    $rewardedVideo_array = array_merge($rewardedVideo_array, $rewardedVideo_object);
                }
            }


            // for ad_RewardedInterstitial //
            $rewardedInterstitial_array = array();
            if (isset($value->ad_RewardedInterstitial)) {
                $decode_value = $value->ad_RewardedInterstitial;
                foreach ($decode_value as $key => $rewardedInterstitial) {
                    $plus = $key + 1;
                    $rewardedInterstitial_object = array("RewardedInterstitial{$plus}" => $rewardedInterstitial);
                    $rewardedInterstitial_array = array_merge($rewardedInterstitial_array, $rewardedInterstitial_object);
                }
            }

            // for ad_AppOpen //
            $appOpen_array = array();
            if (isset($value->ad_AppOpen)) {
                $decode_value = $value->ad_AppOpen;
                foreach ($decode_value as $key => $appOpen) {
                    $plus = $key + 1;
                    $appOpen_object = array("AppOpen{$plus}" => $appOpen);
                    $appOpen_array = array_merge($appOpen_array, $appOpen_object);
                }
            }


            $allAdformat = array_merge($banner_array, $interstitial_array, $native_array, $nativeBanner_array, $rewardedVideo_array, $rewardedInterstitial_array, $appOpen_array);
            $placement = array_merge($new_placement, $allAdformat);
            array_push($placement_array, $placement);
        }

        $new_placement_array = [];
        foreach ($placement_array as $key => $value) {
            if(isset($value['ad_AppID']) && $value['ad_AppID'] == ''){
                unset($value['ad_AppID']);
            }
            unset($value['ad_Banner']);
            unset($value['ad_Interstitial']);
            unset($value['ad_Native']);
            unset($value['ad_NativeBanner']);
            unset($value['ad_RewardedVideo']);
            unset($value['ad_RewardedInterstitial']);
            unset($value['ad_AppOpen']);
            unset($value['platform_adFormat']);
            $covert_obj = (object)$value;
            unset($value['platform_name']);
            $platformName = str_replace(' ', '', $covert_obj->platform_name);
            $object1 = array($platformName => $value);
            $new_placement_array = array_merge($new_placement_array, $object1);
        }

        $response = (object)["STATUS" => $test_response->STATUS, "MSG" => $test_response->MSG, "APP_SETTINGS" => $app_settings, "PLACEMENT" => $new_placement_array,
            "Advertise_List" => $test_response->Advertise_List, "MORE_APP_SPLASH" => $test_response->MORE_APP_SPLASH, "MORE_APP_EXIT" => $test_response->MORE_APP_EXIT, "EXTRA_DATA" => $test_response->app_extra];


        // return $response;

        $redis = Redis::connection('RedisApp6');
        $redis->set($package_name, json_encode($response));

        return 'data set succesfully!';

    }

    public function getDeveloperName()
    {

        $app_details = AppDetails::groupBy('developer')->pluck('developer');
        return $app_details;

    }

    public function searchAppByDeveloper($developer, $status)
    {

        $app_details = AppDetails::where('developer', $developer)->pluck('app_packageName');

        if ($status == 'live') {
            $all_apps = AllApps::whereIn('app_packageName', $app_details)->where('status', 'live')->get();
        } else {
            $all_apps = AllApps::whereIn('app_packageName', $app_details)->get();
        }

        return AllAppResource::collection($all_apps);

    }

    public function getRemovedApp()
    {

        // $companyUser = Auth::user()->company_master_id;
        // if (!$companyUser) {
        //     $allApp = AllApps::latest()->get();
        // } else {
        //     $allApp = AllApps::where('company_master_id', $companyUser)->get();
        // }
        // return AllAppResource::collection($allApp);

        $redis3 = Redis::connection('RedisApp3');
        $response3 = $redis3->keys("*");
        $emptyArray = [];

        $collection = collect($response3);

        $allApps = AllApps::whereIn('app_packageName',$response3)->pluck('app_packageName');

        $diff = $collection->diffKeys($allApps);
        $rr = $diff->all();

        $map = array_map(function($value){
            return  (object)[
                "app_packageName" => $value,
            ];
        },$rr);

        $tt = array_values($map);

        $allApp = AllApps::whereIn('app_packageName',$response3)->latest()->get()->toArray();

        $temp =   array_merge((array)$tt,(array)$allApp);
        $final =   array_values($temp);
        return $final;

    }

    public function generatePackageName($name)
    {

        $app_details_link = "https://suggestqueries.google.com/complete/search?output=chrome&q=" . $name;

        $res = Http::get($app_details_link);
        $result = json_decode($res->getBody()->getContents());
        $array_res = $result[1];
        $newArray = [];
        foreach ($array_res as $array_name) {

            $appRelatedWords = AppRelatedWord::pluck('word');
            $appRelatedWordsConvertArray = $appRelatedWords->toArray();

            $arrayRandomNumber = array_rand($appRelatedWordsConvertArray);
            $randomWord = $appRelatedWordsConvertArray[$arrayRandomNumber];
            $randomWordName = preg_replace('/\s+/', '.', $randomWord);

            $prependArray = ['com', 'in', 'us', 'co.in', 'au'];
            $prependArrayRandomNumber = array_rand($prependArray);
            $prependWord = $prependArray[$prependArrayRandomNumber];

            $journalName = preg_replace('/\s+/', '.', $array_name);
            $newjournalName = $prependWord . '.' . $journalName . '.' . $randomWordName;

            $allApps = AllApps::where('app_packageName', $newjournalName)->first();

            if (!$allApps) {
                $app_details_link = "https://play.google.com/store/apps/details?id=" . $newjournalName;
                $res = Http::get($app_details_link);
                if ($res->status() == 200) {

                } else {
                    array_push($newArray, $newjournalName);
                }
            }

        }
        return $newArray;
    }

    public function getDeletedApp(){

        $allApp = AllApps::onlyTrashed()->get();
        return $allApp;

    }



}
