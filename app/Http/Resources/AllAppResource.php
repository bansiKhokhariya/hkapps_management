<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AllAppResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        if (isset($this->app_parameter) && $this->app_parameter !== null) {
            $app_parameter = json_decode($this->app_parameter);
        } else {
            $app_parameter = [];
        }


        if (isset($this->companyMaster) && $this->companyMaster !== null) {
            $company = $this->companyMaster;
        } else {
            $company = [];
        }

        if($this->appDetails() !== null){

            $developer_name = $this->appDetails()->developer;
            $total_downloads = $this->appDetails()->realInstalls;
            $status = $this->appDetails()->status;
            $ratings = $this->appDetails()->ratings;

        }else{

            $developer_name = '';
            $total_downloads = '';
            $status = '';
            $ratings ='';
        }

        return [
            'id' => isset($this->id) ? $this->id : null,
            'app_no' => isset($this->id) ? $this->id : null,
            'app_logo' => isset($this->app_logo) ? $this->app_logo : null,
            'app_name' => isset($this->app_name) ? $this->app_name : null,
            'app_packageName' => isset($this->app_packageName) ? $this->app_packageName : null,
            'app_accountName' => isset($this->app_accountName) ? $this->app_accountName : null,
            'app_apikey' => isset($this->app_apikey) ? $this->app_apikey : null,
            'app_note' => isset($this->app_note) ? $this->app_note : null,
            'app_updateAppDialogStatus' => isset($this->app_updateAppDialogStatus) ? $this->app_updateAppDialogStatus : null,
            'app_versionCode' => isset($this->app_versionCode) ? $this->app_versionCode : null,
            'app_redirectOtherAppStatus' => isset($this->app_redirectOtherAppStatus) ? $this->app_redirectOtherAppStatus : null,
            'app_newPackageName' => isset($this->app_newPackageName) ? $this->app_newPackageName : null,
            'app_privacyPolicyLink' => isset($this->app_privacyPolicyLink) ? $this->app_privacyPolicyLink : null,
            'app_accountLink' => isset($this->app_accountLink) ? $this->app_accountLink : null,
            'app_extra' =>  isset($this->app_extra)  ? json_decode($this->app_extra) : null,
            'app_adShowStatus' => isset($this->app_adShowStatus) ? $this->app_adShowStatus : null,
            'app_AppOpenAdStatus' => isset($this->app_AppOpenAdStatus) ? $this->app_adShowStatus : null,
            'app_howShowAd' => isset($this->app_howShowAd) ? $this->app_howShowAd : null,
            'app_adPlatformSequence' => isset($this->app_adPlatformSequence) ? json_decode($this->app_adPlatformSequence) : null,
            'app_alternateAdShow' =>isset($this->app_alternateAdShow) ? json_decode($this->app_alternateAdShow) : null,
            'app_testAdStatus' => isset($this->app_testAdStatus) ?$this->app_testAdStatus : null ,
            'app_mainClickCntSwAd' => isset($this->app_mainClickCntSwAd) ? $this->app_mainClickCntSwAd : null,
            'app_innerClickCntSwAd' => isset($this->app_innerClickCntSwAd) ? $this->app_innerClickCntSwAd : null,
            // 'monetize_setting' => $this->AdPlacement(),
            'app_parameter' => $app_parameter,
            'developer' => $developer_name,
            'TotalDownloads' =>intval(str_replace(",","",$total_downloads)),
            'avg_per_day' => 0,
            'status' => isset($this->status) ? $this->status : null,
            // 'unauthorize' => $this->TotalRequestCount(),
            'company_master_id' => $company,
            'ads_master' => isset($this->ads_master) ? $this->ads_master : null,
            'adx' => isset($this->adx) ? $this->adx : null,
            'ratings' => $ratings,
//            'created_at' => $this->created_at->format('d-m-Y'),
//            'updated_at' => $this->updated_at->format('d-m-Y'),
        ];
    }
}
