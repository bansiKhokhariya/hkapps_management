<?php

namespace App\Http\Requests;

use App\Events\RedisDataEvent;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;


class CreateTaskRequest extends FormRequest
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

    public function persist()
    {

        $task = new Task($this->validated());
        $task->refrence = $this->refrence;
        $task->description = $this->description;
        $task->status = 'pending';
        $task->save();

        // call event
//         event(new RedisDataEvent());

        return $task;

    }
}




