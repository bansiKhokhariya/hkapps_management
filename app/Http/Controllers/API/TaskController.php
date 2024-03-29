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
        $authUserName = Auth::user()->name;

        if ($authUserDesignation == 'designer') {
            $task = Task::where('designerStatus', 'pending')->orWhere('designerStatus', 'running')->filter()->get();
        } elseif ($authUserDesignation == 'developer') {
            $task = Task::where('developerStatus', 'pending')->orWhere('developerStatus', 'running')->filter()->get();
        } elseif ($authUserDesignation == 'tester') {
            $task = Task::where('des_testing', 'true')->orWhere('dev_testing', 'true')->filter()->get();
        } else {
            $task = Task::filter()->get();
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

    public function startTask($id, Request $request)
    {

        $authUserName = Auth::user()->name;
        $authUserDesignation = Auth::user()->designation;
        $currentTime = Carbon::now();

        $task = Task::find($id);

        if ($authUserDesignation == 'designer' || $authUserDesignation == "developer" || $authUserDesignation == 'tester' || $authUserDesignation == 'superadmin') {
            if ($authUserDesignation == 'designer') {
                $task->designerStatus = 'running';
                $task->designerStartDate = $currentTime->toDateTimeString();
                $task->assignDesignerName = $authUserName;
                $task->status = 'des-running';
            } elseif ($authUserDesignation == 'developer') {
                $task->developerStatus = 'running';
                $task->developerStartDate = $currentTime->toDateTimeString();
                $task->assignDeveloperName = $authUserName;
                $task->status = 'dev-running';
            } elseif ($authUserDesignation == 'tester') {
                $task->testerStatus = 'running';
                $task->testerStartDate = $currentTime->toDateTimeString();
                $task->assignTesterName = $authUserName;
                $task->status = 'test-running';
            } elseif ($authUserDesignation == 'superadmin') {
                if ($request->designation == 'developer') {
                    $task->developerStatus = 'running';
                    $task->developerStartDate = $currentTime->toDateTimeString();
                    $task->assignDeveloperName = $authUserName;
                    $task->status = 'dev-running';
                } elseif ($request->designation == 'designer') {
                    $task->designerStatus = 'running';
                    $task->designerStartDate = $currentTime->toDateTimeString();
                    $task->assignDesignerName = $authUserName;
                    $task->status = 'des-running';
                } elseif ($request->designation == 'tester') {
                    $task->testerStatus = 'running';
                    $task->testerStartDate = $currentTime->toDateTimeString();
                    $task->assignTesterName = $authUserName;
                    $task->status = 'test-running';
                }
            }
            $task->save();

            // call event
            event(new RedisDataEvent());

            return 'Task Start by ' . $authUserName;
        } else {
            return response('Only the Designer , Developer , Tester and SuperAdmin can start a task!', 404);
        }
    }

    public function taskSendToTester($id)
    {

        $task = Task::find($id);
        $authUserName = Auth::user()->name;
        $authUserDesignation = Auth::user()->designation;

        if ($task->status == 'dev-running') {
            if ($authUserName == $task->assignDeveloperName) {
                if ($task->apkFile) {
                $task->status = 'test-pending';
                $task->dev_testing = 'true';
                }else{
                    return response('You need to add Apk file before sending to tester!', 422);
                }
            } else {
                return response('Only the person who started this task can send to tester!', 404);
            }
        } elseif ($task->status == 'des-running') {
            if ($authUserName == $task->assignDesignerName) {
                if ($task->figmaLink) {
                    $task->status = 'test-pending';
                    $task->des_testing = 'true';
                } else {
                    return response('You need to add figma link before sending to tester!', 422);
                }
            } else {
                return response('Only the person who started this task can send to tester!', 404);
            }
        }
        $task->save();
        // call event
        event(new RedisDataEvent());

        return response('This task send to tester!', 200);
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
                    if ($task->dev_testing == 'completed') {
                        $task->developerStatus = 'completed';
                        $task->developerEndDate = $currentTime->toDateTimeString();
                        if ($task->designerStatus == 'completed') {
                            $task->status = 'completed';
                        } else {
                            $task->status = 'pending';
                        }
                        $task->save();
                        // call event
                        event(new RedisDataEvent());
                        return 'Task Done by ' . $authUserName;
                    } else {
                        return response('You can done this task after tester done it!', 404);
                    }
                } else {
                    return response('Only the person who started This Task can done it', 404);
                }
            } else {
                return response('Only the person who started This Task can done it', 404);
            }
        } elseif ($authUserDesignation == 'designer') {
            if ($task->designerStatus == 'running') {
                if ($task->assignDesignerName == $authUserName) {
                    if ($task->des_testing == 'completed') {
                        if ($task->screenshots) {
                            $ss = json_decode($task->screenshots);
                            $ss_count = count($ss);
                        } else {
                            $ss_count = 0;
                        }
                        if ($task->logo && $task->banner && $ss_count > 2) {
                            $task->designerStatus = 'completed';
                            $task->designerEndDate = $currentTime->toDateTimeString();

                            if ($task->developerStatus == 'completed') {
                                $task->status = 'completed';
                            } else {
                                $task->status = 'pending';
                            }

                            $task->save();


                            //  call event
                            event(new RedisDataEvent());

                            return 'Task Done by ' . $authUserName;
                        } else {
                            return response('you need to upload 1 logo , 1 banner and minimum 2 screenshots before task done!', 422);
                        }
                    } else {
                        return response('You can done this task after tester done it!', 404);
                    }
                } else {
                    return response('Only the person who started This Task can done it', 404);
                }
            } else {
                return response('Only the person who started This Task can done it', 404);
            }
        } elseif ($authUserDesignation == 'superadmin') {
            if ($task->designerStatus == 'running' && $task->status == 'des-running') {
                if ($authUserName == $task->assignDesignerName) {
                    if ($task->screenshots) {
                        $ss = json_decode($task->screenshots);
                        $ss_count = count($ss);
                    } else {
                        $ss_count = 0;
                    }
                    if ($task->logo && $task->banner && $ss_count > 2) {
                        $task->designerStatus = 'completed';
                        $task->designerEndDate = $currentTime->toDateTimeString();

                        if ($task->developerStatus == 'completed') {
                            $task->status = 'completed';
                        } else {
                            $task->status = 'pending';
                        }

                        $task->save();


                        //  call event
                        event(new RedisDataEvent());

                        return 'Task Done by ' . $authUserName;
                    } else {
                        return response('you need to upload 1 logo , 1 banner and minimum 2 screenshots before task done!', 422);
                    }
                } else {
                    return response('Only the person who started This Task can done it', 404);
                }
            } elseif ($task->developerStatus == 'running' && $task->status == 'dev-running') {
                if ($authUserName == $task->assignDeveloperName) {

                    $task->developerStatus = 'completed';
                    $task->developerEndDate = $currentTime->toDateTimeString();
                    if ($task->designerStatus == 'completed') {
                        $task->status = 'completed';
                    } else {
                        $task->status = 'pending';
                    }

                    $task->save();

                    //call event
                    event(new RedisDataEvent());

                    return 'Task Done by ' . $authUserName;
                } else {
                    return response('Only the person who started This Task can done it', 404);
                }
            } elseif ($task->testerStatus == 'running' && $task->status == 'test-running') {
                if ($authUserName == $task->assignTesterName) {
                    if ($task->designerStatus == 'running') {
                        $task->status = 'des-running';
                        $task->des_testing = 'completed';
                        if ($task->dev_testing == 'completed') {
                            $task->testerStatus = 'completed';
                        } else {
                            $task->testerStatus = 'pending';
                        }
                    } elseif ($task->developerStatus == 'running') {
                        $task->status = 'dev-running';
                        $task->dev_testing = 'completed';
                        if ($task->des_testing == 'completed') {
                            $task->testerStatus = 'completed';
                        } else {
                            $task->testerStatus = 'pending';
                        }
                    }
                    $task->save();
                    //call event
                    event(new RedisDataEvent());

                    return 'Task Done by ' . $authUserName;
                } else {
                    return response('Only the person who started This Task can done it', 404);
                }
            }
        } elseif ($authUserDesignation == 'tester') {
            if ($authUserName == $task->assignTesterName) {
                if ($task->designerStatus == 'running') {
                    $task->status = 'des-running';
                    $task->des_testing = 'completed';
                    if ($task->dev_testing == 'completed') {
                        $task->testerStatus = 'completed';
                    } else {
                        $task->testerStatus = 'pending';
                    }
                } elseif ($task->developerStatus == 'running') {
                    $task->status = 'dev-running';
                    $task->dev_testing = 'completed';
                    if ($task->des_testing == 'completed') {
                        $task->testerStatus = 'completed';
                    } else {
                        $task->testerStatus = 'pending';
                    }
                }
                $task->save();
                //call event
                event(new RedisDataEvent());

                return 'Task Done by ' . $authUserName;
            } else {
                return response('Only the person who started This Task can done it', 404);
            }
        } else {
            return response('Only the person who started This Task can done it', 404);
        }
    }

    public function getCompletedTask()
    {
        $authUserName = Auth::user()->name;
        $authDesignation = Auth::user()->designation;
        if ($authDesignation == 'developer') {
            $task = Task::where('assignDeveloperName', $authUserName)->where('developerStatus', 'completed')->get();
        } elseif ($authDesignation == 'designer') {
            $task = Task::where('assignDesignerName', $authUserName)->where('designerStatus', 'completed')->get();
        } elseif ($authDesignation == 'tester') {
            $task = Task::where('assignTesterName', $authUserName)->where('testerStatus', 'completed')->get();
        } else {
            $task = Task::where('status', 'completed')->get();
        }
        return TaskResoruce::collection($task);
    }

    public function deleteLogoBanner(Request $request)
    {
        $id = $request->id;
        if ($request->imageType == 'logo') {
            Task::where('id', $id)
                ->update([
                    'logo' => NULL
                ]);
        } elseif ($request->imageType == 'banner') {
            Task::where('id', $id)
                ->update([
                    'banner' => NULL
                ]);
        }
    }

    public function deleteScreenshots(Request $request)
    {
        $id = $request->id;
        $get_screenshots = Task::where('id', $id)->pluck('screenshots');
        $screenshots = json_decode($get_screenshots);
        $screenshot = json_decode($screenshots[0]);

        if (($key = array_search($request->screenshots, $screenshot)) !== false) {
            unset($screenshot[$key]);
        }

        $file_path[] = $screenshot;
        $new_file_path = $file_path[0];
        $mySSArr = implode(',', $new_file_path);

        $new_array = explode(',', $mySSArr);

        $task = Task::find($id);

        if ($new_array == [""]) {
            $task->screenshots = null;
        } else {
            $task->screenshots = $new_array;
        }

        $task->save();
        return response()->json(['message' => 'Screenshot delete successfully!']);
    }

}
