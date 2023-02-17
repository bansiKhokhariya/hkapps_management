<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SpyAppResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        if($this->getSpyAppDetails()){

            $installs = $this->getSpyAppDetails()->downloads;
            $ratings = $this->getSpyAppDetails()->ratings;
            $reviews = $this->getSpyAppDetails()->reviews;
        }

        return [
            'id'=>$this->id,
            'packageName' =>$this->packageName,
            'url' =>$this->url,
            'locale' =>$this->locale,
            'country' =>$this->country,
            'name' =>$this->name,
            'description' =>$this->description,
            'developerName' =>$this->developerName,
            'icon' =>$this->icon,
            'screenshots' =>json_decode($this->screenshots),
            'score' =>$this->score,
            'priceText' =>$this->priceText,
            'installsText' =>$this->installsText,
            'installs'=> $installs ,
            'ratings'=> $ratings,
            'reviews'=> $reviews,
            'created_at' =>$this->created_at->format('d-m-Y'),
            'updated_at' =>$this->updated_at->format('d-m-Y'),
        ];
    }
}
