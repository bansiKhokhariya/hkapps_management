<?php

namespace App\Http\Requests;

use App\Events\RedisDataEvent;
use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\URL;
use App\Rules\DimensionRule;
use Illuminate\Support\Facades\Validator;


class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'title' => 'required',
            'logo' => 'nullable|dimensions:width=512,height=512',
            'banner' => 'nullable|dimensions:width=1024,height=500',
            'screenshots' => [new DimensionRule()],
        ];
    }

    public function persist(Task $task)
    {
        $myAttchArr = json_decode($task->screenshots);
        $task->fill($this->validated());

        $task->refrence = $this->refrence;
        $task->description = $this->description;
        $task->figmaLink = $this->figmaLink;

        // logo //
        if ($this->hasFile('logo')) {
            $file = $this->file('logo');
            $file_name = $file->getClientOriginalName();
            $file->move(public_path('/task_logo'), $file_name);
            $logo = URL::to('/') . '/task_logo/' . $file_name;
        } else {
            $logo = null;
        }
        if(!$this->hasFile('logo')){
            $task->logo = $task->logo;
        }else{
            $task->logo = $logo;
        }

        // ******** //

        // banner //
        if ($this->hasFile('banner')) {
            $file = $this->file('banner');
            $file_name = $file->getClientOriginalName();
            $file->move(public_path('/task_banner'), $file_name);
            $banner = URL::to('/') . '/task_banner/' . $file_name;
        } else {
            $banner = null;
        }
        if(!$this->hasFile('banner')){
            $task->banner = $task->banner;
        }else{
            $task->banner = $banner;
        }


        // ******* //

        // screenshots //
        if ($this->hasfile('screenshots')) {

            foreach ($this->file('screenshots') as $image) {
                $file_name = $image->getClientOriginalName();
                $image->move(public_path('/task_screenshots'), $file_name);
                $file_path[] = URL::to('/') . '/task_screenshots/' . $file_name;
            }
        }

        if ($task->screenshots == '' || $task->screenshots == null) {

            if (!$this->screenshots) {
                $task->screenshots = NULL;
            } else {
                $myAttchArr1 = json_encode($file_path);
                $task->screenshots = $myAttchArr1;
            }

        } else {

            if ($this->screenshots == null) {
                $task->screenshots = $task->screenshots;
            } else {
                if (!(in_array($this->screenshots, $myAttchArr))) {
                    $result = array_unique(array_merge_recursive($myAttchArr, $file_path));
                    $task->screenshots = json_encode($result);
                }
            }
        }

        // ****** //

        $task->save();

        $todo_ids = $this->todo_ids;
        if ($todo_ids) {
            TodoList::whereIn('id', $todo_ids)
                ->update([
                    'completed' => 'true'
                ]);
        }

        // call event
        event(new RedisDataEvent());

        return $task;

    }
}


