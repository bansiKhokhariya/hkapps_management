<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TestAllAppResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        if($this->app_parameter == null){
            $app_parameter = [];
        }else{
            $app_parameter =  json_decode($this->app_parameter);
        }

        return [
            'id' => $this->id,
            'app_no' => $this->id,
            'app_logo' => $this->app_logo,
            'app_name' => $this->app_name,
            'app_packageName' => $this->app_packageName,
            'app_apikey' => $this->app_apikey,
            'app_note' => $this->app_note,
            'app_updateAppDialogStatus' => $this->app_updateAppDialogStatus,
            'app_versionCode' => $this->app_versionCode,
            'app_redirectOtherAppStatus' => $this->app_redirectOtherAppStatus,
            'app_newPackageName' => $this->app_newPackageName,
            'app_privacyPolicyLink' => $this->app_privacyPolicyLink,
            'app_accountLink' => $this->app_accountLink,
            'app_extra' => json_decode($this->app_extra),
            'app_adShowStatus' => $this->app_adShowStatus,
            'app_AppOpenAdStatus' => $this->app_AppOpenAdStatus,
            'app_howShowAd' => $this->app_howShowAd,
            'app_adPlatformSequence' => json_decode($this->app_adPlatformSequence),
            'app_alternateAdShow' => json_decode($this->app_alternateAdShow),
            'app_testAdStatus' => $this->app_testAdStatus,
            'app_mainClickCntSwAd' => $this->app_mainClickCntSwAd,
            'app_innerClickCntSwAd' => $this->app_innerClickCntSwAd,
            'monetize_setting'=>$this->TestAdPlacement(),
            'app_parameter' => $app_parameter,
            // 'created_at' => $this->created_at->format('d-m-Y'),
            // 'updated_at' => $this->updated_at->format('d-m-Y'),
        ];
    }
}
