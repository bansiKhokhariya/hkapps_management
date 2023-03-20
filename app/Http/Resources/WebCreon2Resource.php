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
            $developer_name = '';
            $total_downloads = '';
            $status = '';
        } else {
            $developer_name = $this->appDetails()->developer;
            $total_downloads = $this->appDetails()->realInstalls;
            $status = $this->appDetails()->status;
        }

        return [
            'app_logo' => $this->app_logo,
            'app_name' => $this->app_name,
            'app_packageName' => $this->app_packageName,
            'app_privacyPolicyLink' => $this->app_privacyPolicyLink,
            'developer_name' => $developer_name,
            'total_downloads' => intval(str_replace(",", "", $total_downloads)),
            'avg_per_day' => 0,
            'status' => $this->status,
            'unauthorize' => $this->TotalRequestCount(),
        ];
    }
}
