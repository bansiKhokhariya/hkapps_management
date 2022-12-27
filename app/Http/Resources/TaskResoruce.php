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

        if($this->attchments == null){
            $attch_count = 0;
        }else{
            $attch_count = count(json_decode($this->attchments));
        }

        if($this->attchments_link == null){
            $attch_link_count = 0;
        }else{
            $attch_link_count = count(json_decode($this->attchments_link));
        }

        return [
            'id' =>$this->id,
            'app_no' =>$this->app_no,
            'title' =>$this->title,
            'package_name' =>$this->package_name,
            'reference_app'=>$this->reference_app,
            'repo_link'=>$this->repo_link,
            'attchments'=>json_decode($this->attchments),
            'attchment_count'=>$this->when($this->attchments||$this->attchments_link !== null, $attch_count+$attch_link_count),
            'attchments_link'=>json_decode($this->attchments_link),
            'console_app'=>$this->console_app,
            'assigned_people'=>$this->user_get(),
            'assign_person'=>$this->User,
            'phase'=>$this->phase,
            'description'=>$this->description,
            'status'=>$this->status,
            'deadline' =>$this->deadline,
            'remark' =>$this->remark,
            'priority'=>$this->priority,
            'assigned_date' =>$this->assigned_date,
            'completed_date' =>$this->completed_date,
            'assign_aso'=>$this->assign_aso,
            'aso_status'=>$this->aso_status,
            'deleted_at'=>$this->deleted_at,
            'created_at' =>$this->created_at->format('d-m-Y h:i:s'),
            'updated_at' =>$this->updated_at->format('d-m-Y h:i:s'),
        ];
    }
}
