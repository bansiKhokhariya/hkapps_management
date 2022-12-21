<?php

namespace App\Http\Requests;

use App\Events\UserEvent;
use App\Models\AllApps;
use App\Models\TestAllApp;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\URL;

class UpdateTestAllAppRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'app_extra' => 'nullable|json',
        ];
    }

    public function persist(TestAllApp $testAllApps)
    {

        $id = Auth::user()->id;
        $auth_user = User::find($id);

        $testAllApps->fill($this->validated());
        // $testAllApps->app_name = $this->app_name;
        // $testAllApps->app_packageName = $this->app_packageName;
        // $testAllApps->app_apikey = $this->app_apikey;
        // $testAllApps->app_note = $this->app_note;
        // app_logo
        // if ($this->hasFile('app_logo')) {
        //     $file = $this->file('app_logo');
        //     $file_name = $file->getClientOriginalName();
        //     $file->move(public_path('/app_logo'), $file_name);
        //     $file_path =  URL::to('/') . '/app_logo/'.$file_name;
        // } else {
        //     $file_path = null;
        // }

        // if(!$this->hasFile('app_logo')){
        //     $testAllApps->app_logo = $this->app_logo;
        // }

        // if($this->app_logo == null){
        //     $testAllApps->app_logo = $testAllApps->logo;
        // }else{
        //     if(($this->hasFile('app_logo'))){
        //         $testAllApps->app_logo = $file_path;
        //     }else{
        //         $testAllApps->app_logo = $this->app_logo;
        //     }
        // }
        // //
        if ($this->app_updateAppDialogStatus == null) {
            $testAllApps->app_updateAppDialogStatus = $testAllApps->app_updateAppDialogStatus;
        } else {
            if (json_decode($this->app_updateAppDialogStatus)) {
                $testAllApps->app_updateAppDialogStatus = 1;
            } else {
                $testAllApps->app_updateAppDialogStatus = 0;
            }
        }
        $testAllApps->app_versionCode = $this->app_versionCode;
        if ($this->app_redirectOtherAppStatus == null) {
            $testAllApps->app_redirectOtherAppStatus = $testAllApps->app_redirectOtherAppStatus;
        } else {
            if (json_decode($this->app_redirectOtherAppStatus)) {
                $testAllApps->app_redirectOtherAppStatus = 1;
            } else {
                $testAllApps->app_redirectOtherAppStatus = 0;
            }
        }
        $testAllApps->app_newPackageName = $this->app_newPackageName;
        $testAllApps->app_privacyPolicyLink = $this->app_privacyPolicyLink;
        // $testAllApps->app_accountLink = $this->app_accountLink;
        if ($this->app_adShowStatus == null) {
            $testAllApps->app_adShowStatus = $testAllApps->app_adShowStatus;
        } else {
            if (json_decode($this->app_adShowStatus)) {
                $testAllApps->app_adShowStatus = 1;
            } else {
                $testAllApps->app_adShowStatus = 0;
            }
        }
        if ($this->app_AppOpenAdStatus == null) {
            $testAllApps->app_AppOpenAdStatus = $testAllApps->app_AppOpenAdStatus;
        } else {
            if (json_decode($this->app_AppOpenAdStatus)) {
                $testAllApps->app_AppOpenAdStatus = 1;
            } else {
                $testAllApps->app_AppOpenAdStatus = 0;
            }
        }
        $testAllApps->app_howShowAd = $this->app_howShowAd;
        $testAllApps->app_adPlatformSequence = $this->app_adPlatformSequence;
        $testAllApps->app_alternateAdShow = $this->app_alternateAdShow;
        if ($this->app_testAdStatus == null) {
            $testAllApps->app_testAdStatus = $testAllApps->app_testAdStatus;
        } else {
            if (json_decode($this->app_testAdStatus)) {
                $testAllApps->app_testAdStatus = 1;
            } else {
                $testAllApps->app_testAdStatus = 0;
            }
        }
        $testAllApps->app_mainClickCntSwAd = $this->app_mainClickCntSwAd;
        $testAllApps->app_innerClickCntSwAd = $this->app_innerClickCntSwAd;


        // app parameter
        // if ($this->name) {
        //     $formulas = [];
        //     for ($i = 0; $i < count($this->name); $i++) {
        //         $formulas[$i] = [
        //          'name' => $this->name[$i],
        //          'value' => $this->value[$i],
        //          'hint' => $this->hint[$i],
        //         ];
        //     }
        //     $allApps->app_parameter = json_encode($formulas);
        // }

        $testAllApps->app_parameter = $this->app_parameter;

        //


        // ad placement //
        // if ($this->platform_id) {
        //     $allApps_id = $allApps->id;
        //     $platform_id = $this->platform_id;
        //     $ad_loadAdIdsType = $this->ad_loadAdIdsType ? $this->ad_loadAdIdsType : [null];
        //     $ad_AppID = $this->ad_AppID ? $this->ad_AppID : [null];
        //     $ad_Banner = $this->ad_Banner ? $this->ad_Banner : [null];
        //     $ad_Interstitial = $this->ad_Interstitial ? $this->ad_Interstitial : [null];
        //     $ad_Native = $this->ad_Native ? $this->ad_Native : [null];
        //     $ad_NativeBanner = $this->ad_NativeBanner ? $this->ad_NativeBanner : [null];
        //     $ad_RewardedVideo = $this->ad_RewardedVideo ? $this->ad_RewardedVideo : [null];
        //     $ad_RewardedInterstitial = $this->ad_RewardedInterstitial ? $this->ad_RewardedInterstitial : [null];
        //     $ad_AppOpen = $this->ad_AppOpen ? $this->ad_AppOpen : [null];


        //     foreach ($platform_id as $key => $no) {
        //         $input['allApps_id'] = $allApps->id;
        //         $input['platform_id'] = $platform_id[$key];
        //         $input['ad_loadAdIdsType'] = isset($ad_loadAdIdsType[$key]) ? $ad_loadAdIdsType[$key] : null;
        //         $input['ad_AppID'] = isset($ad_AppID[$key]) ? $ad_AppID[$key] : null;
        //         $input['ad_Banner'] = isset($ad_Banner[$key]) ? $ad_Banner[$key] : null;
        //         $input['ad_Interstitial'] = isset($ad_Interstitial[$key]) ? $ad_Interstitial[$key] : null;
        //         $input['ad_Native'] = isset($ad_Native[$key]) ? $ad_Native[$key] : null;
        //         $input['ad_NativeBanner'] = isset($ad_NativeBanner[$key]) ? $ad_NativeBanner[$key] : null;
        //         $input['ad_RewardedVideo'] = isset($ad_RewardedVideo[$key]) ? $ad_RewardedVideo[$key] : null;
        //         $input['ad_RewardedInterstitial'] = isset($ad_RewardedInterstitial[$key]) ? $ad_RewardedInterstitial[$key] : null;
        //         $input['ad_AppOpen'] = isset($ad_AppOpen[$key]) ? $ad_AppOpen[$key] : null;

        //         $get_adplacement = AdPlacement::where('allApps_id', $allApps_id)->where('platform_id', $platform_id[$key])->first();
        //         if ($get_adplacement) {
        //             $adplacement = AdPlacement::find($get_adplacement->id);
        //             $adplacement->allApps_id = $input['allApps_id'];
        //             $adplacement->platform_id = $input['platform_id'];
        //             $adplacement->ad_loadAdIdsType = $input['ad_loadAdIdsType'];
        //             $adplacement->ad_AppID = $input['ad_AppID'];
        //             $adplacement->ad_Banner = $input['ad_Banner'];
        //             $adplacement->ad_Interstitial = $input['ad_Interstitial'];
        //             $adplacement->ad_Native = $input['ad_Native'];
        //             $adplacement->ad_NativeBanner = $input['ad_NativeBanner'];
        //             $adplacement->ad_RewardedVideo = $input['ad_RewardedVideo'];
        //             $adplacement->ad_RewardedInterstitial = $input['ad_RewardedInterstitial'];
        //             $adplacement->ad_AppOpen = $input['ad_AppOpen'];
        //             $adplacement->save();
        //         } else {
        //             AdPlacement::create($input);
        //         }
        //     }
        // }
        //


        $testAllApps->save();


        // ***************** view app response json ******************** //
//        $getApp = new AllApps();
//        $result = $getApp->viewResponse($this->app_packageName,$this->app_apikey);
//
//        $redis = Redis::connection('RedisApp6');
//        $redis->set($this->app_packageName, json_encode($result));

        // ************** //

        // call event
        event(new UserEvent($auth_user));

        return $testAllApps;


    }
}
