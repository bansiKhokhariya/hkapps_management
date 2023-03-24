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
        $allApps->app_parameter = $this->app_parameter;
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
