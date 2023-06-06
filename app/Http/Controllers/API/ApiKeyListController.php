<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateApiKeyListRequest;
use App\Http\Requests\UpdateApiKeyListRequest;
use App\Http\Resources\ApiKeyListResource;
use App\Models\AllApps;
use App\Models\ApikeyList;
use Illuminate\Http\Request;
use App\Events\RedisDataEvent;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class ApiKeyListController extends Controller
{
    public function index()
    {
        $apikey_list = ApikeyList::filter()->latest()->get();
        return ApiKeyListResource::collection($apikey_list);
    }

    public function store(CreateApiKeyListRequest $request)
    {
        return ApiKeyListResource::make($request->persist());
    }

    public function show(ApikeyList $apikey_list)
    {
        return ApiKeyListResource::make($apikey_list);
    }

    public function update(UpdateApiKeyListRequest $request, ApikeyList $apikey_list)
    {
        return ApiKeyListResource::make($request->persist($apikey_list));
    }

    public function destroy(ApikeyList $apikey_list)
    {
        $apikey_list->delete();

        // call event
        // event(new RedisDataEvent());

        return response('ApikeyList Deleted Successfully');
    }

    public function assignApiKey(Request $request)
    {

        $package_name = $request->package_name;
        $apikey = $request->apikey;

        $allApps = AllApps::where('app_packageName', $package_name)->first();

        $apikey_json = $allApps->app_apikey;
        $allApps = AllApps::find($allApps->id);
        $old_apikey = $allApps->app_apikey;
        $new_apikey = $request->apikey;
        if ($allApps->app_apikey) {
            $allApps->app_apikey = $old_apikey . ',' . $new_apikey;
        } else {
            $allApps->app_apikey = $request->apikey;
        }
        $allApps->save();


        // ***************** view app response json ******************** //
        $getApp = new AllApps();
        $result = $getApp->viewResponse($package_name, $apikey);

        // $redis = Redis::connection('RedisApp2');
        // $redis->set($package_name, json_encode($result));

        // delete apikey list //
        $apikeyList = ApikeyList::where('apikey_packageName', $package_name)->where('apikey_text', $apikey)->first();
        $apikeyList->forceDelete();

        // call event
        // event(new RedisDataEvent());

        return response()->json('apikey assign succesfully!');

    }

    public function getRedisApiKey()
    {
        $redis3 = Redis::connection('RedisApp3');
        $response3 = $redis3->keys("*");
        $getValue = $redis3->mget($response3);
        $apikeyList = array_map(function ($value) {
            return json_decode($value);
        }, $getValue);


        $apikey = Arr::where($apikeyList, function ($value, $key) {
            if (isset($value->status)) {
                return $value->status == 'unauthorize';
            }
        });


        return array_values($apikey);
    }

    public function setRedisApiKey(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'company_name' => 'required',
            'app_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors())->setStatusCode(422);
        } else {

            $redis3 = Redis::connection('RedisApp3');

            $package_name = $request->package_name;
            $response = $request->jsonData;

            $redis3->set($package_name, $response);

            $redis2 = Redis::connection('RedisApp2');
            $response2 = $redis2->get($package_name);

            $redis6 = Redis::connection('RedisApp6');
            $response6 = $redis6->get($package_name);

            if ($response2 && $response6) {
                return response()->json(['message' => 'app is already exist in server would you override previous settings?', 'status' => true]);
            } else {
                $this->overrideDb2Or6($request->app_name, $package_name);
                return response()->json(['message' => 'data set sucessfully!', 'status' => false]);
            }

        }

    }

    public function overrideDb2Or6($app_name, $package_name)
    {

        $extraData = (object)[
            "status" => false
        ];

        // default database response //
        $app_settings = (object)[
            "app_name" => $app_name,
            "app_accountName" => "Ads Pro",
            "app_accountLink" => "",
            "app_packageName" => $package_name,
            "app_logo" => "",
            "app_status" => "NotPublish",
            "app_privacyPolicyLink" => "https://www.google.com/",
            "app_needInternet" => "0",
            "app_updateAppDialogStatus" => "0",
            "app_versionCode" => "",
            "app_redirectOtherAppStatus" => "0",
            "app_newPackageName" => "",
            "app_dialogBeforeAdShow" => "0",
            "app_adShowStatus" => "1",
            "app_AppOpenAdStatus" => "0",
            "app_howShowAd" => "0",
            "app_adPlatformSequence" => "Admob",
            "app_alernateAdShow" => "",
            "app_howShowAdInterstitial" => "0",
            "app_adPlatformSequenceInterstitial" => "Admob",
            "app_alernateAdShowInterstitial" => "",
            "app_howShowAdNative" => "0",
            "app_adPlatformSequenceNative" => "Admob",
            "app_alernateAdShowNative" => "",
            "app_howShowAdBanner" => "0",
            "app_adPlatformSequenceBanner" => "Admob",
            "app_alernateAdShowBanner" => "",
            "app_mainClickCntSwAd" => "0",
            "app_innerClickCntSwAd" => "0",
            "ShowBanner" => "true",
            "ShowNative" => "true",
            "NativeType" => "custom",
            "NativeColor" => "#000000",
            "SplashAd" => "apop",
            "InterType" => "",
            "QurekaURL" => "https://www.qureka.com",
            "NativeSize" => "100",
            "NoVpnCountry" => "us,ca",
            "FbAppID" => "",
            "FbClientToken" => "",
            "FbLoginProtocol" => "",
            "Preload" => "true",
            "AppType" => "static",
            "StartScreenRepeat" => "10",
            "Vpn" => "false",
            "VpnUrl" => "https://d1ex46a0eaqlz9.cloudfront.net/",
            "VpnId" => "touchvpn",
            "VpnCountryCodearray" => "sg,ch,cz,dk,ie,es,ca,nl,us",
            "VpnCancelCount" => "2",
            "InAppPurchase" => "sub_month,sub_three,sub_six,sub_year",
            "SmallNativeType" => "custom",
            "AdRetryCount" => "2",
            "SmallNativeColor" => "#000000",
            "RecycleNative" => "true"
        ];

        $placement = (object)[

            "Admob" => (object)[
                "ad_showAdStatus" => "1",
                "ad_loadAdIdsType" => "0",
                "AppID" => "ca-app-pub-3940256099942544~3347511713",
                "Banner1" => "ca-app-pub-3940256099942544/6300978111",
                "Interstitial1" => "/6499/example/interstitial",
                "Native1" => "/6499/example/native",
                "RewardedVideo1" => "ca-app-pub-3940256099942544/5224354917",
                "RewardedInterstitial1" => "ca-app-pub-3940256099942544/5354046379",
                "AppOpen1" => "/6499/example/app-open"
            ],
            "Facebookaudiencenetwork" => (object)[
                "ad_showAdStatus" => "1",
                "ad_loadAdIdsType" => "0",
                "Banner1" => "IMG_16_9_APP_INSTALL#YOUR_PLACEMENT_ID",
                "Interstitial1" => "IMG_16_9_APP_INSTALL#YOUR_PLACEMENT_ID",
                "Native1" => "IMG_16_9_APP_INSTALL#YOUR_PLACEMENT_ID",
                "RewardedVideo1" => "IMG_16_9_APP_INSTALL#YOUR_PLACEMENT_ID",
                "NativeBanner1" => "IMG_16_9_APP_INSTALL#YOUR_PLACEMENT_ID"
            ],
            "MyCustomAds" => (object)[
                "ad_showAdStatus" => "1",
                "ad_loadAdIdsType" => "0"
            ],
            "Admob2" => (object)[
                "ad_showAdStatus" => "1",
                "ad_loadAdIdsType" => "0"
            ],
            "Admob3" => (object)[
                "ad_showAdStatus" => "1",
                "ad_loadAdIdsType" => "0",
                "AppID" => "ca-app-pub-3940256099942544~3347511713",
                "Banner1" => "ca-app-pub-3940256099942544/6300978111",
                "Interstitial1" => "/6499/example/interstitial",
                "Native1" => "/6499/example/native",
                "AppOpen1" => "/6499/example/app-open"
            ],
            "Admob4" => (object)[
                "ad_showAdStatus" => "1",
                "ad_loadAdIdsType" => "0",
                "AppID" => "ca-app-pub-3940256099942544~3347511713",
                "Banner1" => "ca-app-pub-3940256099942544/6300978111",
                "Interstitial1" => "/6499/example/interstitial",
                "Native1" => "/6499/example/native",
                "AppOpen1" => "/6499/example/app-open"
            ]
        ];

        $response = (object)["STATUS" => true, "MSG" => "", "APP_SETTINGS" => $app_settings, "PLACEMENT" => $placement,
            "Advertise_List" => [], "MORE_APP_SPLASH" => [], "MORE_APP_EXIT" => [], "EXTRA_DATA" => $extraData];

        // ************** //

        // set default response in redis db 2 //
        $redis2 = Redis::connection('RedisApp2');
        $redis2->set($package_name, json_encode($response));

        // set default response in redis db 6 //
        $redis6 = Redis::connection('RedisApp6');
        $redis6->set($package_name, json_encode($response));

    }

}
