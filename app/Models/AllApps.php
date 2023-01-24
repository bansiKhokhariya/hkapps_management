<?php

namespace App\Models;

use App\Http\Resources\AdPlacementResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Redis;

class AllApps extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $connection = 'mysql4';
    protected $table = 'all_apps';
    protected $guarded = [];


    protected static $logName = 'All Apps';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} All Apps - ID:{$this->id}, appName:{$this->appPackageName}";
    }

    public function companyMaster()
    {
        return $this->belongsTo(CompanyMaster::class);
    }

    public function AdPlacement()
    {

        $adplacement = AdPlacement::where('allApps_id', $this->id)->get();
        return AdPlacementResource::collection($adplacement);

    }

    public function appDetails()
    {

        $app_details = AppDetails::where('app_packageName', $this->app_packageName)->first();
        return $app_details;

    }

    public function TotalRequestCount()
    {
        $get_api_keylist = ApikeyList::where('apikey_packageName', $this->app_packageName)->sum('apikey_request');
        $totalRequest = (int)$get_api_keylist;
        return $totalRequest;
    }

    public function viewResponse($package_name)
    {

        // ***************** view app response json ******************** //

        $all_apps = AllApps::where('app_packageName', $package_name)->first();

        if ($all_apps) {

            $get_app = AllApps::where('app_packageName', $package_name)->select('app_name', 'app_packageName', 'app_logo', 'app_privacyPolicyLink', 'app_updateAppDialogStatus', 'app_versionCode', 'app_redirectOtherAppStatus', 'app_newPackageName', 'app_adPlatformSequence', 'app_adShowStatus', 'app_AppOpenAdStatus', 'app_howShowAd', 'app_alternateAdShow', 'app_mainClickCntSwAd', 'app_innerClickCntSwAd', 'app_apikey', 'app_note', 'app_accountLink', 'app_testAdStatus')->first();
            if ($get_app->app_adPlatformSequence) {
                $get_app->app_adPlatformSequence = join(",", json_decode($get_app->app_adPlatformSequence));
            }
            if ($get_app->app_alternateAdShow) {
                $get_app->app_alternateAdShow = join(",", json_decode($get_app->app_alternateAdShow));
            }

            $get_app_value = json_decode(json_encode($get_app), true);

            $app_data = AllApps::where('app_packageName', $package_name)->first();

            $app_para_decode = json_decode($app_data->app_parameter);
            if ($app_para_decode) {
                $blank_array = [];
                foreach ($app_para_decode as $key => $value) {
                    $object = array($value->name => $value->value);
                    $blank_array = array_merge($blank_array, $object);
                }
            } else {
                $blank_array = [];
            }
            $app_settings = array_merge($get_app_value, $blank_array);
            $extra_data = json_decode($app_data->app_extra);

            $get_placement = AdPlacement::where('allApps_id', $app_data->id)->get();
            $placement_array = [];
            $new_placement_array = [];

            if (isset($get_placement) && count($get_placement) > 0 && $get_placement->isEmpty()) {
                foreach ($get_placement as $key => $value) {

                    $new_placement = json_decode(json_encode($value), true);
                    // for banner //
                    $banner_array = array();
                    if (isset($value->ad_Banner) && $value->ad_Banner !== null) {
                        $decode_value = json_decode($value->ad_Banner);
                        foreach ($decode_value as $key => $banner) {
                            $plus = $key + 1;
                            $banner_object = array("Banner{$plus}" => $banner);
                            $banner_array = array_merge($banner_array, $banner_object);
                        }
                    }

                    // for ad_Interstitial //
                    $interstitial_array = array();
                    if (isset($value->ad_Interstitial) && $value->ad_Interstitial != null) {
                        $decode_value = json_decode($value->ad_Interstitial);
                        foreach ($decode_value as $key => $interstitial) {
                            $plus = $key + 1;
                            $interstitial_object = array("Interstitial{$plus}" => $interstitial);
                            $interstitial_array = array_merge($interstitial_array, $interstitial_object);
                        }
                    }


                    // for native //
                    $native_array = array();
                    if (isset($value->ad_Native) && $value->ad_Native != null) {
                        $decode_value = json_decode($value->ad_Native);
                        foreach ($decode_value as $key => $native) {
                            $plus = $key + 1;
                            $native_object = array("Native{$plus}" => $native);
                            $native_array = array_merge($native_array, $native_object);
                        }
                    }


                    // for ad_NativeBanner //
                    $nativeBanner_array = array();
                    if (isset($value->ad_NativeBanner) && $value->ad_NativeBanner != null) {
                        $decode_value = json_decode($value->ad_NativeBanner);
                        foreach ($decode_value as $key => $native_banner) {
                            $plus = $key + 1;
                            $native_banner_object = array("NativeBanner{$plus}" => $native_banner);
                            $nativeBanner_array = array_merge($nativeBanner_array, $native_banner_object);
                        }
                    }


                    // for ad_RewardedVideo //
                    $rewardedVideo_array = array();
                    if (isset($value->ad_RewardedVideo) && $value->ad_RewardedVideo != null) {
                        $decode_value = json_decode($value->ad_RewardedVideo);
                        foreach ($decode_value as $key => $rewardedVideo) {
                            $plus = $key + 1;
                            $rewardedVideo_object = array("RewardedVideo{$plus}" => $rewardedVideo);
                            $rewardedVideo_array = array_merge($rewardedVideo_array, $rewardedVideo_object);
                        }
                    }


                    // for ad_RewardedInterstitial //
                    $rewardedInterstitial_array = array();
                    if (isset($value->ad_RewardedInterstitial) && $value->ad_RewardedInterstitial != null) {
                        $decode_value = json_decode($value->ad_RewardedInterstitial);
                        foreach ($decode_value as $key => $rewardedInterstitial) {
                            $plus = $key + 1;
                            $rewardedInterstitial_object = array("RewardedInterstitial{$plus}" => $rewardedInterstitial);
                            $rewardedInterstitial_array = array_merge($rewardedInterstitial_array, $rewardedInterstitial_object);
                        }
                    }

                    // for ad_AppOpen //
                    $appOpen_array = array();
                    if (isset($value->ad_AppOpen) && $value->ad_AppOpen != null) {
                        $decode_value = json_decode($value->ad_AppOpen);
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

                foreach ($placement_array as $key => $value) {
                    $value['AppID'] = $value['ad_AppID'];
                    unset($value['id']);
                    unset($value['allApps_id']);
                    unset($value['ad_AppID']);
                    unset($value['platform_id']);
                    unset($value['ad_Banner']);
                    unset($value['ad_Interstitial']);
                    unset($value['ad_Native']);
                    unset($value['ad_NativeBanner']);
                    unset($value['ad_RewardedVideo']);
                    unset($value['ad_RewardedInterstitial']);
                    unset($value['ad_AppOpen']);
                    unset($value['deleted_at']);
                    unset($value['created_at']);
                    unset($value['updated_at']);
                    unset($value['platform']);
                    $covert_obj = (object)$value;
                    unset($value['platform_name']);
                    $platformName = str_replace(' ', '', $covert_obj->platform_name);
                    $object1 = array($platformName => $value);
                    $new_placement_array = array_merge($new_placement_array, $object1);
                }

            }


            $response = (object)['STATUS' => true, 'MSG' => "", 'APP_SETTINGS' => $app_settings, 'PLACEMENT' => $new_placement_array, "EXTRA_DATA" => $extra_data];
            return $response;


        } else {

            $response = (object)['STATUS' => false, 'MSG' => "", 'APP_SETTINGS' => "", 'PLACEMENT' => "", "EXTRA_DATA" => ""];
            return $response;

        }
        // *********** //

    }


}
