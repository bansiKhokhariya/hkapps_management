<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdxResource extends JsonResource
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
            'id' => $this->id,
            'adx_register_company' => $this->adx_register_company,
            'adx' => $this->adx,
            'adx_share' => $this->adx_share,
            'type' => $this->type,
            'company_master_id' => $company,
            'deleted_at'=>$this->deleted_at,
            'created_at' => $this->created_at->format('d-m-Y'),
            'updated_at' => $this->updated_at->format('d-m-Y'),
        ];
    }
}
