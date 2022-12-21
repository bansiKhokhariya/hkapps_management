<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityHistoryResource extends JsonResource
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
            'activity_id' => $this->id,
            'activity_userName' => $this->causer->name,
            'activity_task'=>$this->description,
            'Date' => $this->created_at->format('d-m-Y h:i:s A'),
        ];
    }
}
