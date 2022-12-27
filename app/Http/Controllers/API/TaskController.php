<?php

namespace App\Http\Controllers\API;

use App\Events\UserEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResoruce;
use App\Models\Task;
use App\Models\TaskUser;
use App\Models\Time;
use App\Models\User;
use App\Notifications\assignPersonNotification;
use App\Notifications\TaskDoneNotification;
use App\Notifications\TaskReWorkingNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Notifiable;


class TaskController extends Controller
{
    public function index(Task $task)
    {

        $id = Auth::user()->id;
        $role = Auth::user()->roles;
        if ($role == 'admin') {
            $task = Task::get();
        } else {
            $task = Task::where('assign_person', $id)->get();
        }
        return TaskResoruce::collection($task);

    }

    public function store(CreateTaskRequest $request, Task $task)
    {

        $task = TaskResoruce::make($request->persist());

        // add time for user //
        if ((int)$request->assign_person) {
            $get_time = Time::where('task_id', $task->id)->where('user_id', $request->assign_person)->first();
            if ($get_time) {
                $time = Time::find($get_time->id);
                $time->task_id = $task->id;
                $time->user_id = $request->assign_person;
                date_default_timezone_set("Asia/Kolkata");
                $time->is_started = false;
                $time->assigned_date = $task->assigned_date;
                $time->save();
            } else {
                $time = new Time();
                $time->task_id = $task->id;
                $time->user_id = $request->assign_person;
                date_default_timezone_set("Asia/Kolkata");
                $time->is_started = false;
                $time->assigned_date = $task->assigned_date;
                $time->save();
            }
        }

        // add time for aso//
        if ($task->assign_aso) {
            $time = new Time();
            $time->task_id = $task->id;
            $time->user_id = $task->assign_aso;
            date_default_timezone_set("Asia/Kolkata");
            $time->is_started = false;
            $time->assigned_date = $task->assigned_date;
            $time->save();
        }

        // attch user //
        $user = User::find(json_decode($request->assign_person));
        $task->users()->attach($user);

        return $task;

    }

    public function show(Task $task)
    {
        return TaskResoruce::make($task);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {

        $task = TaskResoruce::make($request->persist($task));

        // add time for aso//
        if ($task->assign_aso) {
            $get_time = Time::where('task_id', $task->id)->where('user_id', $task->assign_aso)->first();
            if ($get_time) {
                $time = Time::find($get_time->id);
                $time->task_id = $task->id;
                $time->user_id = $task->assign_aso;
                date_default_timezone_set("Asia/Kolkata");
                $time->is_started = false;
                $time->assigned_date = $task->assigned_date;
                $time->save();
            } else {
                $time = new Time();
                $time->task_id = $task->id;
                $time->user_id = $task->assign_aso;
                date_default_timezone_set("Asia/Kolkata");
                $time->is_started = false;
                $time->assigned_date = $task->assigned_date;
                $time->save();
            }
        }


        $task = Task::find($task->id);
        $myPersonArr = explode(',', $task->assigned_people);
        if (!(in_array($task->assign_aso, $myPersonArr))) {
            $newArr = array_push($myPersonArr, $task->assign_aso);

            $task->assigned_people = implode(",", $myPersonArr);

            $taskAttch = Task::find($task->id);
            $user = User::find(json_decode($task->assign_aso));
            $taskAttch->users()->attach($user);
        }
        $task->save();

//        $task->users()->sync(json_decode($request->assign_people));
        return $task;

    }

    public function destroy(Task $task)
    {

        $get_task = Task::where('id', $task->id)->get();
        $getTime = Time::where('task_id', $get_task[0]->id)->where('user_id', $get_task[0]->assign_person)->first();

        if ($getTime && ($getTime->is_started)) {
            $to = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $getTime->start_date);
            $from = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', date('d-m-Y H:i:s'));
            $diff_in_days = $to->diffInMilliseconds($from);

            $getTime->time = $diff_in_days + $getTime->time;
            $getTime->is_started = false;
            $getTime->save();
            $task->delete();
            return response('Task Deleted Successfully');
        } else {
            $task->delete();
            return response('Task Deleted Successfully');
        }

    }

    public function getDeleteTask()
    {

        $task = Task::onlyTrashed()->get();
        return response()->json($task);

    }

    public function getDeleteTaskShow($id)
    {

        $task = Task::onlyTrashed()->find($id);
        return response()->json($task);

    }

    public function getUserTask(Request $request)
    {
        $task = Task::where('assign_person', $request->user_id)->with('assigned_people')->get();
        return TaskResoruce::collection($task);

    }

    public function getTaskTime(Request $request)
    {
        date_default_timezone_set("Asia/Kolkata");
        $get_time = Time::where('task_id', $request->task_id)->where('user_id', $request->user_id)->select('time')->get();

        $time = Time::where('task_id', $request->task_id)->where('user_id', $request->user_id)->get();
        // if ($time[0]->end_date == null) {
        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $time[0]->assigned_date);
        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
        $diff_in_days = $to->diffForHumans($from);
        // }
        // else {
        //     $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $time[0]->assigned_date);
        //     $from = $time[0]->end_date;
        //     $diff_in_days = $to->diffForHumans($from);
        // }

        return response()->json(['time' => $get_time[0]->time, 'days' => $diff_in_days]);
    }

    public function task_start(Request $request, $task_id)
    {
        // for event
        $id = Auth::user()->id;
        $auth_user = User::find($id);
        //

        $get_task = Task::where('id', $task_id)->get();

        $getTaskStart = Time::where('user_id', $get_task[0]->assign_person)->where('is_started', 1)->first();

        $getTime = Time::where('task_id', $task_id)->where('user_id', $get_task[0]->assign_person)->first();
        if ($getTaskStart == null) {
            if (is_null($getTime)) {

                $start_time = new Time();
                $start_time->task_id = $task_id;
                $start_time->user_id = $get_task[0]->assign_person;
                date_default_timezone_set("Asia/Kolkata");
                $start_time->start_date = date('d-m-Y H:i:s');
                $start_time->is_started = true;
                $start_time->save();
                $task = Task::find($task_id);
                $task->status = 'working';
                $task->save();

                // change status in task_user
                $task_user = TaskUser::where('task_id', $get_task[0]->id)->where('user_id', $get_task[0]->assign_person)->first();
                $update_task_user = TaskUser::find($task_user->id);
                $update_task_user->status = 'working';
                $update_task_user->save();

                return response()->json($start_time);
            } else {

                $stopTime = $getTime;
                $stopTime->is_started = true;
                date_default_timezone_set("Asia/Kolkata");
                $stopTime->start_date = date('d-m-Y H:i:s');
                $stopTime->stop_date = null;
                $stopTime->save();
                $task = Task::find($task_id);
                $task->status = 'working';
                $task->save();

                // change status in task_user
                $task_user = TaskUser::where('task_id', $get_task[0]->id)->where('user_id', $get_task[0]->assign_person)->first();
                $update_task_user = TaskUser::find($task_user->id);
                $update_task_user->status = 'working';
                $update_task_user->save();

                //event call
                // event(new UserEvent($auth_user));

                return response()->json($stopTime);
            }
        } else {
            return response()->json(['message' => "You are currently working on one task"], 403);
        }

    }

    public function stop_task(Request $request, $task_id)
    {

        // for event
        $id = Auth::user()->id;
        $auth_user = User::find($id);
        //

        $get_task = Task::where('id', $task_id)->get();
        $getTime = Time::where('task_id', $task_id)->where('user_id', $get_task[0]->assign_person)->first();

        $time = $getTime;
        date_default_timezone_set("Asia/Kolkata");

        if ($time->stop_date == null) {

            $to = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $time->start_date);
            $from = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', date('d-m-Y H:i:s'));
            $diff_in_days = $to->diffInMilliseconds($from);

            if ($time->time == null) {
                $time->time = $diff_in_days;
            } else {
                $time->time = $diff_in_days + $time->time;
            }
        }

        $time->is_started = false;
        $time->stop_date = date('d-m-Y H:i:s');
        $time->save();

        $task = Task::find($task_id);
        $task->status = 'pending';
        $task->save();

        // change status in task_user
        $task_user = TaskUser::where('task_id', $get_task[0]->id)->where('user_id', $get_task[0]->assign_person)->first();
        $update_task_user = TaskUser::find($task_user->id);
        $update_task_user->status = 'pending';
        $update_task_user->save();

        //event call
        // event(new UserEvent($auth_user));

        return response()->json($time);

    }

    public function ready_testing_task(Request $request, $task_id)
    {
        // for event
        $id = Auth::user()->id;
        $auth_user = User::find($id);
        //

        $get_task = Task::where('id', $task_id)->get();
        $getTime = Time::where('task_id', $task_id)->where('user_id', $get_task[0]->assign_person)->first();

        $time = $getTime;

        date_default_timezone_set("Asia/Kolkata");

        if (!($time->is_started)) {
            $time->time = (int)($time->time);
        } elseif ($time->end_date == null) {
            $to = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $time->start_date);
            $from = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', date('d-m-Y H:i:s'));
            $diff_in_days = $to->diffInMilliseconds($from);
            if ($time->time == null) {
                $time->time = (int)($diff_in_days);
            } else {
                $time->time = (int)($diff_in_days + $time->time);
            }
        } elseif ($time->end_date !== null) {
            $to = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $time->start_date);
            $from = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', date('d-m-Y H:i:s'));
            $diff_in_days = $to->diffInMilliseconds($from);
            if ($time->time == null) {
                $time->time = (int)($diff_in_days);
            } else {
                $time->time = (int)($diff_in_days + $time->time);
            }
        }

        $time->is_started = false;
        $time->end_date = date('d-m-Y H:i:s');
        $time->save();


        // change status in task_user
        $task_user = TaskUser::where('task_id', $get_task[0]->id)->where('user_id', $get_task[0]->assign_person)->first();
        $update_task_user = TaskUser::find($task_user->id);
        $update_task_user->status = 'ready_testing';
        $update_task_user->save();


        // assign tester
        $get_tester = User::where('designation', 'tester')->get();

        $get_tester_id = $get_tester[0]->id;

        $task = Task::find($task_id);
        $task->assign_person = $get_tester_id;

        $myPersonArr = explode(',', $task->assigned_people);
        if (!(in_array($get_tester[0]->id, $myPersonArr))) {
            $newArr = array_push($myPersonArr, $get_tester[0]->id);

            $task->assigned_people = implode(",", $myPersonArr);

            $taskAttch = Task::find($task->id);
            $user = User::find(json_decode($get_tester[0]->id));
            $taskAttch->users()->attach($user);
        }

        if ($get_tester[0]->designation == 'tester') {
            if ($task->getphase() == 'designing') {
                $task->phase = "testing_designing";
            } elseif ($task->getphase() == 'developing') {
                $task->phase = "testing_developing";
            }
        }
        $task->prev_assign_person = $get_task[0]->assign_person;
        $task->status = 'pending';
        $task->assigned_date = date('Y-m-d H:i:s');
        $task->save();


        // add time for tester//

        $tester_time = Time::where('task_id', $task->id)->where('user_id', $task->assign_person)->first();
        if ($tester_time) {
            $time = Time::find($tester_time->id);
            $time->start_date = null;
            $time->end_date = null;
            $time->stop_date = null;
            $time->is_started = false;
            $time->save();
        } else {
            $time = new Time();
            $time->task_id = $task->id;
            $time->user_id = $task->assign_person;
            date_default_timezone_set("Asia/Kolkata");
            $time->is_started = false;
            $time->assigned_date = $task->assigned_date;
            $time->save();
        }


        // send notification assign user //
        if ($get_tester_id !== null) {
            $user = User::where('id', $get_tester_id)->get();
            $notification = $user[0];
            $task_details = [
                'task_id' => $task->id,
                'app_no' => $task->app_no,
                'task_title' => $task->title,
                'assign_person' => $task->assign_person,
            ];
            $notification->notify(new assignPersonNotification($task_details, $auth_user));
        }


        //event call
        // event(new UserEvent($auth_user));

        return response()->json($time);

    }

    public function done_task(Request $request, $task_id)
    {

        // for event
        $id = Auth::user()->id;
        $auth_user = User::find($id);
        //

        $get_task = Task::where('id', $task_id)->get();

        $getTime = Time::where('task_id', $task_id)->where('user_id', $get_task[0]->assign_person)->first();

        $assign_person = $get_task[0]->assign_person;
        $prev_assign_person = (int)$get_task[0]->prev_assign_person;


        $time = $getTime;

        date_default_timezone_set("Asia/Kolkata");

        if (!($time->is_started)) {
            $time->time = (int)($time->time);
        } elseif ($time->end_date == null) {
            $to = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $time->start_date);
            $from = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', date('d-m-Y H:i:s'));
            $diff_in_days = $to->diffInMilliseconds($from);
            if ($time->time == null) {
                $time->time = (int)($diff_in_days);
            } else {
                $time->time = (int)($diff_in_days + $time->time);
            }
        } elseif ($time->end_date !== null) {
            $to = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $time->start_date);
            $from = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', date('d-m-Y H:i:s'));
            $diff_in_days = $to->diffInMilliseconds($from);
            if ($time->time == null) {
                $time->time = (int)($diff_in_days);
            } else {
                $time->time = (int)($diff_in_days + $time->time);
            }
        }

        $time->is_started = false;
        $time->end_date = date('d-m-Y H:i:s');
        $time->save();

        $task = Task::find($task_id);
        $task->assign_person = null;
        $task->status = 'done';
        $task->save();


        // change status in task_user for user
        $task_user = TaskUser::where('task_id', $get_task[0]->id)->where('user_id', $prev_assign_person)->first();
        $update_task_user = TaskUser::find($task_user->id);
        $update_task_user->status = 'done';
        $update_task_user->save();

        // change status in task_user for user
        $task_user_tester = TaskUser::where('task_id', $get_task[0]->id)->where('user_id', $assign_person)->first();
        $task_user_tester = TaskUser::find($task_user_tester->id);
        $task_user_tester->status = 'done';
        $task_user_tester->save();


        // send notification user task done //
        if ($get_task[0]->prev_assign_person !== null) {
            $user = User::where('id', $get_task[0]->prev_assign_person)->get();
            $notification = $user[0];
            $task_details = [
                'task_id' => $task->id,
                'app_no' => $task->app_no,
                'task_title' => $task->title,
            ];
            $notification->notify(new TaskDoneNotification($task_details, $auth_user));
        }

        // send notification admin task done //
        $user = User::where('roles', 'admin')->get();
        $task_details = [
            'task_id' => $task->id,
            'app_no' => $task->app_no,
            'task_title' => $task->title,
        ];

        foreach ($user as $notification) {
            $notification->notify(new TaskDoneNotification($task_details, $auth_user));
        }


        //event call
        // event(new UserEvent($auth_user));

        return response()->json($time);
    }

    public function task_reworking(Request $request, $task_id)
    {

        // for event
        $id = Auth::user()->id;
        $auth_user = User::find($id);
        //

        $get_task = Task::where('id', $task_id)->get();
        $getTime = Time::where('task_id', $task_id)->where('user_id', $get_task[0]->assign_person)->first();


        date_default_timezone_set("Asia/Kolkata");

        if (!($getTime->is_started)) {
            $getTime->time = (int)($getTime->time);
//        } elseif ($getTime->end_date == null) {
        } else {
            $to = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $getTime->start_date);
            $from = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', date('d-m-Y H:i:s'));
            $diff_in_days = $to->diffInMilliseconds($from);
            if ($getTime->time == null) {
                $getTime->time = (int)($diff_in_days);
            } else {
                $getTime->time = (int)($diff_in_days + $getTime->time);
            }

            $getTime->end_date = date('d-m-Y H:i:s');
            $getTime->is_started = false;
            $getTime->save();


            $get_user = User::where('id', $get_task[0]->prev_assign_person)->get();

            $task = Task::find($task_id);

            if ($get_user[0]->designation == 'developer') {
                $task->phase = 'developing';
            } elseif ($get_user[0]->designation == 'developer') {
                $task->phase = 'developing';
            }
            $task->assign_person = $get_task[0]->prev_assign_person;
            $task->prev_assign_person = $get_task[0]->assign_person;
            $task->status = 're-working';
            $task->save();


            // change status in task_user
            $task_user = TaskUser::where('task_id', $get_task[0]->id)->where('user_id', $get_task[0]->assign_person)->first();
            $update_task_user = TaskUser::find($task_user->id);
            $update_task_user->status = 're-working';
            $update_task_user->save();


            // send notification user task re-working //
            if ($get_task[0]->prev_assign_person !== null) {
                $user = User::where('id', $get_task[0]->prev_assign_person)->get();
                $notification = $user[0];
                $task_details = [
                    'task_id' => $task->id,
                    'app_no' => $task->app_no,
                    'task_title' => $task->title,
                ];
                $notification->notify(new TaskReWorkingNotification($task_details, $auth_user));
            }


            //event call
            // event(new UserEvent($auth_user));

            return response()->json(['message' => "task status change re-working"]);
        }
//        } else {
//            return response()->json(['message' => "could not change this task status re-working"]);
//        }

    }

    public function get_app_no()
    {

        $total_task = Task::withTrashed()->get();
        $total_task_count = Task::withTrashed()->count();
        return response()->json(['app_no' => $total_task[$total_task_count - 1]->app_no + 1]);

    }

    public function task_status_change(Request $request, $task_id)
    {

        // for event
        $id = Auth::user()->id;
        $auth_user = User::find($id);
        //

        $task = Task::find($task_id);

        if ($request->status === 'pending') {
            $getTime = Time::where('task_id', $task_id)->where('user_id', $task->assign_person)->first();
            if ($getTime) {
                $getTime->end_date = null;
                $getTime->save();
            }
        }

        $task->status = $request->status;
        $task->save();

        //event call
        // event(new UserEvent($auth_user));

        return response()->json(['message' => "task status change"]);

    }

    public function ready_testing_task_show($prev_person_id)
    {
        $get_task = Task::where('prev_assign_person', $prev_person_id)->with('assigned_people')->get();

        $collection = collect($get_task);

        $filtered = $collection->filter(function ($value, $key) {
            if ($value->user) {
                return $value->user->designation == 'tester';
            }
        })->values();

        return $filtered;

    }

    public function user_done_task_show($prev_person_id)
    {

        $task_user = TaskUser::where('user_id',$prev_person_id)->where('status','done')->pluck('task_id');
        $task = Task::whereIn('id', $task_user)->get();
        return TaskResoruce::collection($task);

    }

    public function tester_done_task_show()
    {

        $task = Task::where('status', 'done')->get();
        return TaskResoruce::collection($task);

    }

    public function reworking_task_show($prev_person_id)
    {
        $task = Task::where('prev_assign_person', $prev_person_id)->where('status', 're-working')->get();
        return TaskResoruce::collection($task);

    }

    public function deleteAttchment(Request $request, $id)
    {
        $get_attchments = Task::where('id', $id)->pluck('attchments');
        $attchments = json_decode($get_attchments);
        $attchment = json_decode($attchments[0]);


        if (($key = array_search($request->attchment, $attchment)) !== false) {
            unset($attchment[$key]);
        }

        $file_path[] = $attchment;
        $new_file_path = $file_path[0];
        $myAttchArr = implode(',', $new_file_path);

        $new_array = explode(',', $myAttchArr);


        $task = Task::find($id);

        if ($new_array == [""]) {
            $task->attchments = null;
        } else {
            $task->attchments = $new_array;
        }

        $task->save();
        return response()->json(['message' => 'attchment delete successfully!']);

    }

    public function aso_task_start(Request $request, $task_id)
    {

        // for event
        $id = Auth::user()->id;
        $auth_user = User::find($id);
        //

        $get_task = Task::where('id', $task_id)->get();

        $getTaskStart = Time::where('user_id', $get_task[0]->assign_aso)->where('is_started', 1)->first();

        $getTime = Time::where('task_id', $task_id)->where('user_id', $get_task[0]->assign_aso)->first();
        if ($getTaskStart == null) {
            if (is_null($getTime)) {
                $start_time = new Time();
                $start_time->task_id = $task_id;
                $start_time->user_id = $get_task[0]->assign_aso;
                date_default_timezone_set("Asia/Kolkata");
                $start_time->start_date = date('d-m-Y H:i:s');
                $start_time->is_started = true;
                $start_time->save();
                $task = Task::find($task_id);
                $task->aso_status = 'working';
                $task->save();
                return response()->json($start_time);
            } else {
                $stopTime = $getTime;
                $stopTime->is_started = true;
                date_default_timezone_set("Asia/Kolkata");
                $stopTime->start_date = date('d-m-Y H:i:s');
                $stopTime->stop_date = null;
                $stopTime->save();
                $task = Task::find($task_id);
                $task->aso_status = 'working';
                $task->save();


                // change status in task_user
                $task_user = TaskUser::where('task_id', $get_task[0]->id)->where('user_id', $get_task[0]->assign_aso)->first();
                $update_task_user = TaskUser::find($task_user->id);
                $update_task_user->status = 'working';
                $update_task_user->save();


                //event call
                // event(new UserEvent($auth_user));

                return response()->json($stopTime);
            }
        } else {
            return response()->json(['message' => "You are currently working on one task"], 403);
        }
    }

    public function aso_task_stop(Request $request, $task_id)
    {

        // for event
        $id = Auth::user()->id;
        $auth_user = User::find($id);
        //

        $get_task = Task::where('id', $task_id)->get();
        $getTime = Time::where('task_id', $task_id)->where('user_id', $get_task[0]->assign_aso)->first();

        $time = $getTime;
        date_default_timezone_set("Asia/Kolkata");

        if ($time->stop_date == null) {

            $to = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $time->start_date);
            $from = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', date('d-m-Y H:i:s'));
            $diff_in_days = $to->diffInMilliseconds($from);

            if ($time->time == null) {
                $time->time = $diff_in_days;
            } else {
                $time->time = $diff_in_days + $time->time;
            }
        }

        $time->is_started = false;
        $time->stop_date = date('d-m-Y H:i:s');
        $time->save();

        $task = Task::find($task_id);
        $task->aso_status = 'pending';
        $task->save();


        // change status in task_user
        $task_user = TaskUser::where('task_id', $get_task[0]->id)->where('user_id', $get_task[0]->assign_aso)->first();
        $update_task_user = TaskUser::find($task_user->id);
        $update_task_user->status = 'pending';
        $update_task_user->save();


        //event call
        // event(new UserEvent($auth_user));

        return response()->json($time);

    }

    public function aso_task_done(Request $request, $task_id)
    {

        // for event
        $id = Auth::user()->id;
        $auth_user = User::find($id);
        //

        $get_task = Task::where('id', $task_id)->get();

        $getTime = Time::where('task_id', $task_id)->where('user_id', $get_task[0]->assign_aso)->first();

        $time = $getTime;

        date_default_timezone_set("Asia/Kolkata");

        if (!($time->is_started)) {
            $time->time = (int)($time->time);
        } elseif ($time->end_date == null) {
            $to = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $time->start_date);
            $from = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', date('d-m-Y H:i:s'));
            $diff_in_days = $to->diffInMilliseconds($from);
            if ($time->time == null) {
                $time->time = (int)($diff_in_days);
            } else {
                $time->time = (int)($diff_in_days + $time->time);
            }
        } elseif ($time->end_date !== null) {
            $to = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $time->start_date);
            $from = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', date('d-m-Y H:i:s'));
            $diff_in_days = $to->diffInMilliseconds($from);
            if ($time->time == null) {
                $time->time = (int)($diff_in_days);
            } else {
                $time->time = (int)($diff_in_days + $time->time);
            }
        }

        $time->is_started = false;
        $time->end_date = date('d-m-Y H:i:s');
        $time->save();

        $task = Task::find($task_id);
        $task->aso_status = 'done';
        $task->save();

        // change status in task_user
        $task_user = TaskUser::where('task_id', $get_task[0]->id)->where('user_id', $get_task[0]->assign_aso)->first();
        $update_task_user = TaskUser::find($task_user->id);
        $update_task_user->status = 'done';
        $update_task_user->save();

        // send notification admin task done //
        $user = User::where('roles', 'admin')->get();
        $task_details = [
            'task_id' => $task->id,
            'app_no' => $task->app_no,
            'task_title' => $task->title,
        ];

        foreach ($user as $notification) {
            $notification->notify(new TaskDoneNotification($task_details, $auth_user));
        }

        //event call
        // event(new UserEvent($auth_user));

        return response()->json($time);
    }

    public function getAsoTask(Request $request)
    {

        $task = Task::where('assign_aso', $request->user_id)->get();
        return TaskResoruce::collection($task);

    }

    public function employeeTaskStatus(Request $request)
    {
        $user_id = $request->user_id;

        if ($request->from_date && $request->to_date) {
            $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->from_date);
            $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->to_date);
        }

        if ($request->from_date && $request->to_date && (!($request->user_id))) {

            $task = Task::whereBetween('created_at', [$startDate, $endDate])->get();

            $total_count = $task->count();
            $pending_count = $task->where('status', 'pending')->count();
            $working_count = $task->where('status', 'working')->count();
            $ready_testing_count = $task->where('status', 'ready_testing')->count();
            $done_count = $task->where('status', 'done')->count();
            $re_working_count = $task->where('status', 're_working')->count();


        } elseif ($request->user_id && (!($request->from_date && $request->to_date))) {


            $task_user = TaskUser::where('user_id', $user_id)->get();

            $total_count = $task_user->count();
            $pending_count = $task_user->where('status', 'pending')->count();
            $working_count = $task_user->where('status', 'working')->count();
            $ready_testing_count = $task_user->where('status', 'ready_testing')->count();
            $done_count = $task_user->where('status', 'done')->count();
            $re_working_count = $task_user->where('status', 're_working')->count();


//            $task_user = Task::get();
//
//            $user = User::where('id', $request->user_id)->get();
//
//            $collection = collect($task_user);
//            $filtered = $collection->filter(function ($value, $key) use ($user_id) {
//                $myPersonArr = explode(',', $value->assigned_people);
//                if ($myPersonArr) {
//                    return (in_array($user_id, $myPersonArr));
//                }
//            })->values();
//
//
//            if ($user[0]->designation == 'ASO') {
//
//                $total_count = $filtered->count();
//                $pending_count = $filtered->where('aso_status', 'pending')->count();
//                $working_count = $filtered->where('aso_status', 'working')->count();
//                $done_count = $filtered->where('aso_status', 'done')->count();
//                $ready_testing_count = $filtered->where('aso_status', 'ready_testing')->count();
//                $re_working_count = $filtered->where('aso_status', 're_working')->count();
//
//            } else {
//
//                $total_count = $filtered->count();
//                $pending_count = $filtered->where('status', 'pending')->count();
//                $working_count = $filtered->where('status', 'working')->count();
//                $ready_testing_count = $filtered->where('status', 'ready_testing')->count();
//                $done_count = $filtered->where('status', 'done')->count();
//                $re_working_count = $filtered->where('status', 're_working')->count();
//
//            }

        } elseif ($request->user_id && $request->from_date && $request->to_date) {


            $task_user = TaskUser::where('user_id', $user_id)->whereBetween('created_at', [$startDate, $endDate])->get();

            $total_count = $task_user->count();
            $pending_count = $task_user->where('status', 'pending')->count();
            $working_count = $task_user->where('status', 'working')->count();
            $ready_testing_count = $task_user->where('status', 'ready_testing')->count();
            $done_count = $task_user->where('status', 'done')->count();
            $re_working_count = $task_user->where('status', 're_working')->count();



//            $task_user = Task::whereBetween('created_at', [$startDate, $endDate])->get();
//            $user = User::where('id', $request->user_id)->get();
//
//
//            $collection = collect($task_user);
//            $filtered = $collection->filter(function ($value, $key) use ($user_id) {
//                $myPersonArr = explode(',', $value->assigned_people);
//                if ($myPersonArr) {
//                    return (in_array($user_id, $myPersonArr));
//                }
//            })->values();
//
//            if ($user[0]->designation == 'ASO') {
//
//                $total_count = $filtered->count();
//                $pending_count = $filtered->where('aso_status', 'pending')->count();
//                $working_count = $filtered->where('aso_status', 'working')->count();
//                $ready_testing_count = $filtered->where('aso_status', 'ready_testing')->count();
//                $done_count = $filtered->where('aso_status', 'done')->count();
//                $re_working_count = $filtered->where('aso_status', 're_working')->count();
//
//            } else {
//
//                $total_count = $filtered->count();
//                $pending_count = $filtered->where('status', 'pending')->count();
//                $working_count = $filtered->where('status', 'working')->count();
//                $ready_testing_count = $filtered->where('status', 'ready_testing')->count();
//                $done_count = $filtered->where('status', 'done')->count();
//                $re_working_count = $filtered->where('status', 're_working')->count();
//
//            }

        } else {

            $task = Task::get();

            $total_count = $task->count();
            $pending_count = $task->where('status', 'pending')->count();
            $working_count = $task->where('status', 'working')->count();
            $ready_testing_count = $task->where('status', 'ready_testing')->count();
            $done_count = $task->where('status', 'done')->count();
            $re_working_count = $task->where('status', 're_working')->count();

        }

        return response()->json(['total_count' => $total_count, 'pending_count' => $pending_count, 'working_count' => $working_count, 'ready_testing_count' => $ready_testing_count, 'done_count' => $done_count, 're_working_count' => $re_working_count]);

    }

    public function employeeTask(Request $request)
    {

        $user_id = $request->user_id;

        if ($request->from_date && $request->to_date) {
            $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->from_date);
            $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->to_date);
        }

        if ($request->from_date && $request->to_date && (!($request->user_id))) {

            $task = Task::whereBetween('created_at', [$startDate, $endDate])->with('assigned_people', 'assign_person')->get();

        } elseif ($request->user_id && (!($request->from_date && $request->to_date))) {

            $task_user = Task::with('assigned_people', 'assign_person')->get();

            $collection = collect($task_user);
            $task = $collection->filter(function ($value, $key) use ($user_id) {
                $myPersonArr = explode(',', $value->assigned_people);
                if ($myPersonArr) {
                    return (in_array($user_id, $myPersonArr));
                }
            })->values();

        } elseif ($request->user_id && $request->from_date && $request->to_date) {

            $task_user = Task::whereBetween('created_at', [$startDate, $endDate])->with('assigned_people', 'assign_person')->get();

            $collection = collect($task_user);
            $task = $collection->filter(function ($value, $key) use ($user_id) {
                $myPersonArr = explode(',', $value->assigned_people);
                if ($myPersonArr) {
                    return (in_array($user_id, $myPersonArr));
                }
            })->values();

        } else {

            $task = Task::with('assigned_people', 'assign_person')->get();

        }

        return $task;

    }

}





