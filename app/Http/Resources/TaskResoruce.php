<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResoruce extends JsonResource
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
            'srno' =>$this->id,
            'title' =>$this->title,
            'refrence' =>$this->refrence,
            'description' =>$this->description,
            'status' =>$this->status,
            'deleted_at'=>$this->deleted_at,
            'created_at' =>$this->created_at->format('d-m-Y h:i:s'),
            'updated_at' =>$this->updated_at->format('d-m-Y h:i:s'),
        ];
    }
}
