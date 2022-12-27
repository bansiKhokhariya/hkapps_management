<?php

namespace App\Http\Requests;

use App\Events\UserEvent;
use App\Models\AdPlacement;
use App\Models\AllApps;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\URL;


class UpdateAllAppRequest extends FormRequest
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

    public function persist(AllApps $allApps)
    {

        $id = Auth::user()->id;
        $auth_user = User::find($id);

        $allApps->fill($this->validated());
        // $allApps->app_name = $this->app_name;
        // $allApps->app_packageName = $this->app_packageName;
//         $allApps->app_apikey = $this->app_apikey;
        // $allApps->app_note = $this->app_note;


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
        //     $allApps->app_logo = $this->app_logo;
        // }

        // if($this->app_logo == null){
        //     $allApps->app_logo = $allApps->app_logo;
        // }else{
        //     if(($this->hasFile('app_logo'))){
        //         $allApps->app_logo = $file_path;
        //     }else{
        //         $allApps->app_logo = $this->app_logo;
        //     }
        // }
        //

        if ($this->app_updateAppDialogStatus == null) {
            $allApps->app_updateAppDialogStatus = $allApps->app_updateAppDialogStatus;
        } else {
            if (json_decode($this->app_updateAppDialogStatus)) {
                $allApps->app_updateAppDialogStatus = 1;
            } else {
                $allApps->app_updateAppDialogStatus = 0;
            }
        }
        $allApps->app_versionCode = $this->app_versionCode;
        if ($this->app_redirectOtherAppStatus == null) {
            $allApps->app_redirectOtherAppStatus = $allApps->app_redirectOtherAppStatus;
        } else {
            if (json_decode($this->app_redirectOtherAppStatus)) {
                $allApps->app_redirectOtherAppStatus = 1;
            } else {
                $allApps->app_redirectOtherAppStatus = 0;
            }
        }
        $allApps->app_newPackageName = $this->app_newPackageName;
        $allApps->app_privacyPolicyLink = $this->app_privacyPolicyLink;
        // $allApps->app_accountLink = $this->app_accountLink;
        if ($this->app_adShowStatus == null) {
            $allApps->app_adShowStatus = $allApps->app_adShowStatus;
        } else {
            if (json_decode($this->app_adShowStatus)) {
                $allApps->app_adShowStatus = 1;
            } else {
                $allApps->app_adShowStatus = 0;
            }
        }
        if ($this->app_AppOpenAdStatus == null) {
            $allApps->app_AppOpenAdStatus = $allApps->app_AppOpenAdStatus;
        } else {
            if (json_decode($this->app_AppOpenAdStatus)) {
                $allApps->app_AppOpenAdStatus = 1;
            } else {
                $allApps->app_AppOpenAdStatus = 0;
            }
        }
        $allApps->app_howShowAd = $this->app_howShowAd;
        $allApps->app_adPlatformSequence = $this->app_adPlatformSequence;
        $allApps->app_alternateAdShow = $this->app_alternateAdShow;
        if ($this->app_testAdStatus == null) {
            $allApps->app_testAdStatus = $allApps->app_testAdStatus;
        } else {
            if (json_decode($this->app_testAdStatus)) {
                $allApps->app_testAdStatus = 1;
            } else {
                $allApps->app_testAdStatus = 0;
            }
        }
        $allApps->app_mainClickCntSwAd = $this->app_mainClickCntSwAd;
        $allApps->app_innerClickCntSwAd = $this->app_innerClickCntSwAd;


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

        $allApps->app_parameter = $this->app_parameter;

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


        $allApps->save();



        // ***************** view app response json ******************** //
        $getApp = new AllApps();
        $result = $allApps->viewResponse($this->app_packageName);

        $redis = Redis::connection('RedisApp');
        $redis->set($this->app_packageName, json_encode($result));

        // call event
        // event(new UserEvent($auth_user));

        return $allApps;


    }
}
