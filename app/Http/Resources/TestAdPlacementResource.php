<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TestAdPlacementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'allApps_id' => $this->allApps_id,
            'platform_id' => $this->platform_id,
            'platform_name' => $this->platform->platform_name,
            'platform_adFormat' => json_decode($this->platform->ad_format),
            'ad_loadAdIdsType' => $this->ad_loadAdIdsType,
            'ad_AppID' => $this->ad_AppID,
            'ad_Banner' => json_decode($this->ad_Banner),
            'ad_Interstitial' => json_decode($this->ad_Interstitial),
            'ad_Native' => json_decode($this->ad_Native),
            'ad_NativeBanner' => json_decode($this->ad_NativeBanner),
            'ad_RewardedVideo' => json_decode($this->ad_RewardedVideo),
            'ad_RewardedInterstitial' => json_decode($this->ad_RewardedInterstitial),
            'ad_AppOpen' => json_decode($this->ad_AppOpen),
//            'created_at' => $this->created_at->format('d-m-Y'),
//            'updated_at' => $this->updated_at->format('d-m-Y'),
        ];
    }
}
