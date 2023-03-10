<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlatformResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->companyMaster == null) {
            $company = [];
        } else {
            $company = $this->companyMaster;
        }
        return [
            'id' =>$this->id,
            'logo'=>$this->logo,
            'platform_name'=>$this->platform_name,
            'ad_format'=>json_decode($this->ad_format),
            'company_master_id' => $company,
            'created_at' =>$this->created_at->format('d-m-Y'),
            'updated_at' =>$this->updated_at->format('d-m-Y'),
        ];
    }
}
