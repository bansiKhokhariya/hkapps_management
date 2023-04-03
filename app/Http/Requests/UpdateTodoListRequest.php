<?php

namespace App\Http\Requests;

use App\Models\TodoList;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTodoListRequest extends FormRequest
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
        ];
    }

    public function persist(TodoList $todoList)
    {
        $todoList->fill($this->validated());
        $todoList->completed = $this->completed;
        $todoList->save();
        return $todoList;
    }
}
