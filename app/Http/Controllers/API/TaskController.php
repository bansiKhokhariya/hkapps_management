<?php

namespace App\Http\Controllers\API;

use App\Events\RedisDataEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResoruce;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class TaskController extends Controller
{
    public function index(Task $task)
    {

        $authUserDesignation = Auth::user()->designation;

        if ($authUserDesignation == 'designer') {
            $task = Task::where('designerStatus', 'pending')->orWhere('designerStatus', 'running')->get();
        } elseif ($authUserDesignation == 'developer') {
            $task = Task::where('developerStatus', 'pending')->orWhere('developerStatus', 'running')->get();
        } else {
            $task = Task::all();
        }

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

    public function startTask($id)
    {

        $authUserName = Auth::user()->name;
        $authUserDesignation = Auth::user()->designation;
        $currentTime = Carbon::now();

        $task = Task::find($id);

        if ($authUserDesignation == 'designer' || $authUserDesignation == "developer") {
            if ($authUserDesignation == 'designer') {
                $task->designerStatus = 'running';
                $task->designerStartDate = $currentTime->toDateTimeString();
                $task->assignDesignerName = $authUserName;
            } elseif ($authUserDesignation == 'developer') {
                $task->developerStatus = 'running';
                $task->developerStartDate = $currentTime->toDateTimeString();
                $task->assignDeveloperName = $authUserName;
            }
            $task->status = 'running';
            $task->save();

            // call event
            event(new RedisDataEvent());

            return 'Task Start by ' . $authUserName;
        } elseif ($authUserDesignation == 'superadmin') {

            $task->status = 'running';
            $task->save();

        } else {
            return response('Only the Designer , Developer and SuperAdmin can start a task!', 404);

        }


    }

    public function endTask($id)
    {

        $authUserName = Auth::user()->name;
        $authUserDesignation = Auth::user()->designation;
        $currentTime = Carbon::now();
        $task = Task::find($id);
        if ($authUserDesignation == 'developer') {
            if ($task->developerStatus == 'running') {
                if ($task->assignDeveloperName == $authUserName) {
                    $task->developerStatus = 'completed';
                    $task->developerEndDate = $currentTime->toDateTimeString();
                    if ($task->designerStatus == 'completed') {
                        $task->status = 'completed';
                    }
                    $task->save();

                    // call event
                    event(new RedisDataEvent());

                    return 'Task Done by ' . $authUserName;
                } else {
                    return response('Only the person who started This Task can done it', 404);
                }
            } else {
                return response('Only the person who started This Task can done it', 404);
            }

        } elseif ($authUserDesignation == 'designer') {

            if ($task->designerStatus == 'running') {
                if ($task->assignDesignerName == $authUserName) {
                    $task->designerStatus = 'completed';
                    $task->designerEndDate = $currentTime->toDateTimeString();
                    if ($task->developerStatus == 'completed') {
                        $task->status = 'completed';
                    }
                    $task->save();

                    // call event
                    event(new RedisDataEvent());

                    return 'Task Done by ' . $authUserName;
                } else {
                    return response('Only the person who started This Task can done it', 404);
                }
            } else {
                return response('Only the person who started This Task can done it', 404);
            }
        } elseif ($authUserDesignation == 'superadmin') {
            if ($task->status == 'running') {
                if ($task->assignSuperAdminName == $authUserName) {
                    $task->status = 'completed';
                    $task->save();

                    // call event
                    event(new RedisDataEvent());

                    return 'Task Done by ' . $authUserName;
                } else {
                    return response('Only the person who started This Task can done it', 404);
                }
            } else {
                return response('Only the person who started This Task can done it', 404);
            }
        } else {
            return response('Only the person who started This Task can done it', 404);
        }
    }

}





