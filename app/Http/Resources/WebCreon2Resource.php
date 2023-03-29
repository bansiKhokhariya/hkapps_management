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
            $status = '';
            $developer = '';
        } else {
            $total_downloads = $this->appDetails()->realInstalls;
            $status = $this->appDetails()->status;
            $developer = $this->appDetails()->developer;
        }

        return [
            'icon' => $this->app_logo,
            'app_accountName' => $this->app_accountName,
            'package_name' => $this->app_packageName,
            'title' => $this->app_name,
            'developer_name' =>$developer,
            'total_downloads' => intval(str_replace(",", "", $total_downloads)),
            'avg_per_day' => 0,
            'status' => $status,
            'unauthorize' => $this->TotalRequestCount(),
        ];
    }
}
