<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AllConsoleResource extends JsonResource
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
            'manageBy' => $this->manageBy(),
            'email' => $this->email,
            'password' => $this->password,
            'consoleName' => $this->consoleName,
            'status' => $this->status,
            'mobile' => $this->mobile,
            'device' => $this->device,
            'remarks' => $this->remarks,
            'blogger' => $this->blogger,
            'privacy' => $this->privacy,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }
}

