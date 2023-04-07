<?php

namespace App\Http\Requests;

use App\Events\RedisDataEvent;
use App\Models\DefaultTodo;
use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use App\Models\GitHubToken;


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


        $defaultTodos = DefaultTodo::all();
        if($defaultTodos){
            foreach($defaultTodos as $defaultTodo){
                $todo = TodoList::where('task_id', $task->id)->where('category', $defaultTodo->category)->where('todoName', $defaultTodo->todoName)->first();
                if (!$todo) {
                    $todoList = new TodoList();
                    $todoList->task_id = $task->id;
                    $todoList->todoName = $defaultTodo->todoName;
                    $todoList->category = $defaultTodo->category;
                    $todoList->completed = 'false';
                    $todoList->save();
                }
            }
        }


        // create github repo //
        $name = str_replace(' ', '_', strtolower($task->title));
        $gitRepoName = $task->id . '_' . $name;
        $getToken = GitHubToken::find(1);
        $getTokenNew = str_replace("\r\n", '', $getToken->github_access_token);

        $token = 'Bearer ' . $getTokenNew;

        $response = Http::withHeaders([
            'Authorization' => $token,
            'Content-Type' => 'application/json',
        ])->post('https://api.github.com/user/repos', [
            'name' => $gitRepoName,
            'description' => 'hkApps Task Repo',
        ])->json();


//        $error = $response['errors'][0]['message'];
        $gettask = Task::find($task->id);
        $gettask->githubRepoLink = $response['clone_url'];
        $gettask->save();

        // ************* //


        // call event
        event(new RedisDataEvent());

        return $task;

    }
}




