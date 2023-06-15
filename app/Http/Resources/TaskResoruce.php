<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResoruce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        // developer profile //
        $developer_name = $this->assignDeveloperName;
        if ($developer_name) {
            $user = User::where('name', $developer_name)->first();
            $developer_profile = $user->profile_image;
        } else {
            $developer_profile = null;
        }


        // designer profile //
        $designer_name = $this->assignDesignerName;
        if ($designer_name) {
            $user = User::where('name', $designer_name)->first();
            $designer_profile = $user->profile_image;
        } else {
            $designer_profile = null;
        }

        // tester profile //
        $tester_name = $this->assignTesterName;
        if ($tester_name) {
            $user = User::where('name', $tester_name)->first();
            $tester_profile = $user->profile_image;
        } else {
            $tester_profile = null;
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'refrence' => $this->refrence,
            'description' => $this->description,
            'status' => $this->status,
            'developerStatus' => $this->developerStatus,
            'developerStartDate' => $this->developerStartDate,
            'developerEndDate' => $this->developerEndDate,
            'assignDeveloperName' => $this->assignDeveloperName,
            'developerProfile' => $developer_profile,
            'designerStatus' => $this->designerStatus,
            'designerStartDate' => $this->designerStartDate,
            'designerEndDate' => $this->designerEndDate,
            'assignDesignerName' => $this->assignDesignerName,
            'designerProfile' => $designer_profile,
            'testerStatus' => $this->testerStatus,
            'assignTesterName' => $this->assignTesterName,
            'testerProfile' => $tester_profile,
            'githubRepoLink' => $this->githubRepoLink,
            'dev_testing' => $this->dev_testing,
            'des_testing' => $this->des_testing,
            'figmaLink' => $this->figmaLink,
            'logo' => $this->logo,
            'banner' => $this->banner,
            'apkFile' => $this->apkFile,
            'screenshots' => json_decode($this->screenshots),
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at->format('d-m-Y h:i:s'),
            'updated_at' => $this->updated_at->format('d-m-Y h:i:s'),
        ];
    }
}
