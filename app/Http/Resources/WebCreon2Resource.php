<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebCreon2Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        if ($this->appDetails() == null) {
            $total_downloads = '';
        } else {
            $total_downloads = $this->appDetails()->realInstalls;
        }

        return [
            'icon' => $this->icon,
            'app_accountName' => $this->app_accountName,
            'package_name' => $this->package_name,
            'developer_name' => $this->developer,
            'total_downloads' => intval(str_replace(",", "", $total_downloads)),
            'avg_per_day' => 0,
            'unauthorize' => $this->TotalRequestCount(),
        ];
    }
}
