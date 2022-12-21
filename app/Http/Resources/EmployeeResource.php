<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'id' =>$this->id,
            'name' =>$this->name,
            'email' =>$this->email,
            'roles' =>$this->roles,
            'profile_image' =>$this->profile_image,
            'total_task'=>$this->getTotalCountAttribute(),
            'pending_task'=>$this->getPendingCountAttribute(),
            'working_task'=>$this->getWorkingCountAttribute(),
            'done_task'=>$this->getdoneCountAttribute(),
            'reworking_task'=>$this->getReWorkingCountAttribute(),
            'task'=> $this->getTaskAttribute(),
            'designation' =>$this->designation,
            'created_at' =>$this->created_at->format('d-m-Y'),
            'updated_at' =>$this->updated_at->format('d-m-Y'),
        ];
    }
}
