<?php

namespace App\Http\Requests;

use App\Models\TodoList;
use Illuminate\Foundation\Http\FormRequest;

class CreateTodoListRequest extends FormRequest
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
            'todoName' => 'required',
            'task_id' => 'required',
            'category' => 'required',
        ];
    }

    public function persist()
    {
        $todoList = new TodoList($this->validated());
        $todoList->category = $this->category;
        $todoList->completed = 'false';
        $todoList->save();
        return $todoList;
    }
}
