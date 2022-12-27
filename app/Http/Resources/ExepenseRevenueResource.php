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
        return [
            'id' => $this->id,
            'package_name' => $this->package_name ,
            'ads_master' => $this->ads_master,
            'total_invest' => $this->total_invest,
            'adx' => $this->adx,
            'revenue' => $this->revenue,
        ];
    }
}
