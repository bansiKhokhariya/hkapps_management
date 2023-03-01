<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SpyAppResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        if ($this->getSpyAppDetails()) {

            $installs = $this->getSpyAppDetails()->downloads;
            $ratings = $this->getSpyAppDetails()->ratings;
            $reviews = $this->getSpyAppDetails()->reviews;

        }else{

            $installs = null;
            $ratings = null;
            $reviews = null;
        }

        $released_date = Carbon::parse($this->released);
        $updated_date = Carbon::parse($this->updated);


        $mix_age = now()->diff(Carbon::parse($released_date));

        if ($mix_age->y > 0) {
            $age = $mix_age->y . " years";
        } elseif ($mix_age->m > 0) {
            $age = $mix_age->m . " months";
        } elseif ($mix_age->d > 0) {
            $age = $mix_age->d . " days";
        } else {
            $age = '0 days';
        }


        return [
            'id' => $this->id,
            'packageName' => $this->packageName,
            'url' => $this->url,
            'locale' => $this->locale,
            'country' => $this->country,
            'name' => $this->name,
            'description' => $this->description,
            'developerName' => $this->developerName,
            'icon' => $this->icon,
            'screenshots' => json_decode($this->screenshots),
            'score' => $this->score,
            'priceText' => $this->priceText,
            'installsText' => $this->installsText,
            'installs' => $installs,
            'ratings' => $ratings,
            'reviews' => $reviews,
            'released_day' => $released_date->diffForHumans(),
            'updated_day' => $updated_date->diffForHumans(),
            'Age' => $age,
            'released' => $this->released,
            'updated' => $this->updated,
            'category'=> $this->category,
            'version'=>$this->version,
            'dailyInstalls' => $this->getInstalls($this->packageName),
            'AvgDailyInstalls' => $this->getAvgDailyInstalls($this->packageName),
            'created_at' => $this->created_at->format('d-m-Y'),
            'updated_at' => $this->updated_at->format('d-m-Y'),
        ];
    }
}
