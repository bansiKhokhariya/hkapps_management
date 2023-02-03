<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExepenseRevenueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        if($this->ads_master){
            $ads = $this->masterAds;
        }else{
            $ads = null;
        }

        if($this->adx){
            $adx = $this->Adx_master;
        }else{
            $adx = null;
        }
        if ($this->companyMaster == null) {
            $company = [];
        } else {
            $company = $this->companyMaster;
        }

        return [
            'id' => $this->id,
            'package_name' => $this->package_name,
            'ads_master' => $ads,
            'adx' => $adx,
            'total_invest' => $this->total_invest,
            'icon' => $this->icon(),
            'revenue' => $this->revenue,
            'company_master_id' => $company,
            'created_date' => $this->created_at->format('d-m-Y'),
        ];
    }
}
