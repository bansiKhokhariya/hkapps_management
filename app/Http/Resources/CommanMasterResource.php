<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommanMasterResource extends JsonResource
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
            'vpn_id' => $this->vpn_id,
            'value' => $this->value,
            'type' => $this->type,
            'deleted_at'=>$this->deleted_at,
            'created_at' => $this->created_at->format('d-m-Y'),
            'updated_at' => $this->updated_at->format('d-m-Y'),
        ];
    }
}
