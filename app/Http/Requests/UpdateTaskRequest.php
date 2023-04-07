<?php

namespace App\Http\Requests;

use App\Events\RedisDataEvent;
use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\URL;


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
        ];
    }

    public function persist(Task $task)
    {

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
        $task->logo = $logo;
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
        $task->banner = $banner;
        // ******* //

        // screenshots //
        if ($this->hasfile('screenshots')) {

            foreach ($this->file('screenshots') as $image) {
                $file_name = $image->getClientOriginalName();
                $image->move(public_path('/task_screenshots'), $file_name);
                $file_path[] = URL::to('/') . '/task_screenshots/' . $file_name;
            }
        }

        if (!$task->screenshots) {
            if (!$this->screenshots) {
                $task->screenshots = NULL;
            } else {
                $myAttchArr1 = json_encode($file_path);
                $task->screenshots = $myAttchArr1;
            }
        } else {
            $myAttchArr = json_decode($task->screenshots);
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


