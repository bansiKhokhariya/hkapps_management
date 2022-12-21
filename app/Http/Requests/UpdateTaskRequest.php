<?php

namespace App\Http\Requests;

use App\Events\UserEvent;
use App\Models\Task;
use App\Models\Time;
use App\Models\User;
use App\Notifications\assignPersonNotification;
use GuzzleHttp\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
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
            'app_no' => 'required',
            'title' => 'required',
            'package_name' => 'required',
            'reference_app' => 'required',
        ];
    }

    public function persist(Task $task)
    {
        // for event//
        $id = Auth::user()->id;
        $auth_user = User::find($id);

        $task->fill($this->validated());


        // for send notification //
        $prev_preson = $task->assign_person;


        // for date //
        date_default_timezone_set("Asia/Kolkata");


        // attchments //
        $new_folder_path = $task->app_no;

        if ($this->hasfile('attchments')) {
            foreach ($this->file('attchments') as $image) {
                $name = $image->getClientOriginalName();
                $withOutExtension = pathinfo($name, PATHINFO_FILENAME);
                $extension = $image->getClientOriginalExtension();
                $image->move(public_path() . '/task/' . $new_folder_path . '/', $withOutExtension . '_' . date("d-m-Y") . '.' . $extension);
                $file_path[] = URL::to('/') . '/task/' . $this->app_no . '/' . $withOutExtension . '_' . date("d-m-Y") . '.' . $extension;
            }
        }


        if (!$task->attchments) {
            if (!$this->attchments) {
                $task->attchments = NULL;
            } else {
                $myAttchArr1 = json_encode($file_path);
                $task->attchments = $myAttchArr1;
            }
        } else {
            $myAttchArr = json_decode($task->attchments);
            if ($this->attchments == null) {
                $task->attchments = $task->attchments;
            } else {
                if (!(in_array($this->attchments, $myAttchArr))) {
                    $result = array_unique(array_merge_recursive($myAttchArr, $file_path));
                    $task->attchments = json_encode($result);
                }
            }
        }

        //  attchments link //
        if (!$task->attchments_link) {
            if (!$this->attchments_link) {
                $task->attchments_link = NULL;
            } else {
                $task->attchments_link = implode(", ", [$this->attchments_link]);
            }
        } else {
            $myAttchLinkArr = json_decode($task->attchments_link);
            $myAttchLinkArrNew = json_decode($this->attchments_link);
            if ($this->attchments_link == null) {
                $task->attchments_link = $task->attchments_link;
            } else {
                if (!(in_array($this->attchments_link, $myAttchLinkArr))) {
                    $result = array_unique(array_merge_recursive($myAttchLinkArr, $myAttchLinkArrNew));
                    $task->attchments_link = json_encode($result);
                }
            }
        }


        // status //
        if ($this->assign_person == null){
            $task->status = $task->status;
        }elseif((int)$this->assign_person !== (int)$task->assign_person){
            $task->status = 'pending';
        }


        // assigned date //
        if ($this->assign_person == null) {
            $task->assigned_date = null;
        } elseif ($this->assign_person == $task->assign_person) {
            $task->assigned_date = $task->assigned_date;
        } else {
            $task->assigned_date = date('Y-m-d H:i:s');
        }

        // assign person//
        $task->assign_person = $this->assign_person;

        // phase //
        if ($task->assign_person !== null) {
            if ($task->User->designation == 'designer') {
                $task->phase = "designing";
            } elseif ($task->User->designation == 'developer') {
                $task->phase = "developing";
            } elseif ($task->User->designation == 'tester') {
                if ($task->getphase() == 'designing') {
                    $task->phase = "testing_designing";
                } elseif ($task->getphase() == 'developing') {
                    $task->phase = "testing_developing";
                }
            }
        }

        // assign_aso  and  aso_status //
        if ($task->assign_person !== null) {
            if ($task->User->designation == 'developer') {
                $aso_user = User::where('designation','ASO')->get();
                if(!$task->assign_aso){
                    $task->aso_status = 'pending';
                }
                $task->assign_aso = $aso_user[0]->id;
            }
        }


        // assigned people //
        $myPersonArr = explode(',', $task->assigned_people);
        if (!(in_array($this->assign_person, $myPersonArr))) {

            $newArr = array_push($myPersonArr, $this->assign_person);

            $task->assigned_people = implode(",", $myPersonArr);

            $taskAttch = Task::find($task->id);
            $user = User::find(json_decode($this->assign_person));
            $taskAttch->users()->attach($user);
        }

        $task->console_app = $this->console_app;
        $task->description = $this->description;
        $task->deadline = $this->deadline;
        $task->priority = $this->priority;


        // add time for user//
        if((int)$prev_preson !== (int)$this->assign_person){
            $time = new Time();
            $time->task_id = $task->id;
            $time->user_id = $this->assign_person;
            date_default_timezone_set("Asia/Kolkata");
            $time->is_started = false;
            $time->assigned_date = $task->assigned_date;
            $time->save();
        }

        $task->save();


        //  send notification assign user //
        if($task->assign_person !== null){
            if ((int)$this->assign_person !== $prev_preson) {
                $user = User::where('id', $this->assign_person)->get();
                $notification = $user[0];
                $task_details = [
                    'task_id' => $task->id,
                    'app_no' => $task->app_no,
                    'task_title' => $task->title,
                    'assign_person' => $task->assign_person,
                ];
                $notification->notify(new assignPersonNotification($task_details, $auth_user));
            }
        }

        // call event
        event(new UserEvent($auth_user));

        return $task;

    }
}


