<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->causer_id) {
            $userName = User::where('id', $this->causer_id)->first();
            $causer = $userName->name;
        } else {
            $causer = 'unauthorized';
        }

        return [
            'activity_id' => $this->id,
            'activity_userName' => $causer,
            'activity_task' => $this->description,
            'Date' => $this->created_at->format('d-m-Y h:i:s A'),
        ];
    }
}
