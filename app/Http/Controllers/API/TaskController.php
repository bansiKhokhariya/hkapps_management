<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResoruce;
use App\Models\Task;
use Illuminate\Http\Request;



class TaskController extends Controller
{
    public function index(Task $task)
    {
        $task = Task::all();
        return TaskResoruce::collection($task);
    }

    public function store(CreateTaskRequest $request)
    {
        return TaskResoruce::make($request->persist());
    }

    public function show(Task $task)
    {
        return TaskResoruce::make($task);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        return TaskResoruce::make($request->persist($task));
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response('Task Deleted Successfully');
    }


}





