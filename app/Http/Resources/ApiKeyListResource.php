<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiKeyListResource extends JsonResource
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
            'apikey_text' => $this->apikey_text,
            'apikey_packageName' => $this->apikey_packageName,
            'apikey_appID' => $this->apikey_appID,
            'apikey_request' => $this->apikey_request,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
