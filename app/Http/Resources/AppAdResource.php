<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppAdResource extends JsonResource
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
            'ad_id' =>$this->id,
            'app_name' =>$this->app_name,
            'app_packageName' =>$this->app_packageName,
            'app_logo' =>$this->app_logo,
            'app_banner' =>$this->app_banner,
            'app_shortDecription' =>$this->app_shortDecription,
            'app_buttonName' =>$this->app_buttonName,
            'app_rating' =>$this->app_rating,
            'app_download' =>$this->app_download,
            'app_AdFormat' =>json_decode($this->app_AdFormat),
            'created_at' =>$this->created_at->format('d-m-Y'),
            'updated_at' =>$this->updated_at->format('d-m-Y'),
        ];
    }
}
