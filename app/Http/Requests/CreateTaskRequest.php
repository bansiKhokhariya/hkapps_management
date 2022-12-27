<?php

namespace App\Http\Requests;


use App\Events\UserEvent;
use App\Models\Task;
use App\Models\User;
use App\Notifications\assignPersonNotification;
use GuzzleHttp\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

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
            'app_no' => 'required|unique:task,app_no',
            'title' => 'required',
            'package_name' => 'required|unique:task,package_name',
            'reference_app' => 'required',
        ];
    }

    public function persist()
    {
        //for date//
        date_default_timezone_set("Asia/Kolkata");

        // for event //
        $id = Auth()->user()->id;
        $auth_user = User::find($id);

        $task = new Task($this->validated());


        // attchments //
        $new_folder_path = $this->app_no;

        if ($this->hasfile('attchments')) {

            foreach ($this->file('attchments') as $image) {
                $name = $image->getClientOriginalName();
                $withOutExtension = pathinfo($name, PATHINFO_FILENAME);
                $extension =  $image->getClientOriginalExtension();
                $image->move(public_path().'/task/'.$new_folder_path.'/', $withOutExtension.'_'.date("d-m-Y").'.'.$extension);
                $file_path[] = URL::to('/') . '/task/'.$this->app_no.'/'.$withOutExtension.'_'.date("d-m-Y").'.'.$extension;
            }
        }

        if ($this->attchments) {
            $task->attchments = json_encode($file_path);
        } else {
            $task->attchments = null;
        }

        // assign person //
        $task->assign_person = $this->assign_person;

        // assigned_date //
        if ($this->assign_person == null) {
            $task->assigned_date = null;
        } else {
            $task->assigned_date = date('Y-m-d H:i:s');
        }

        // assigned_people //
        if ($this->assign_person == null) {
            $task->assigned_people = null;
        } else {
            $task->assigned_people = implode(",", [$this->assign_person]);
        }


        // attchments_link //
        if ($this->attchments_link == null) {
            $task->attchments_link = null;
        } else {
            $task->attchments_link = implode(", ", [$this->attchments_link]);
        }


        // repo_link //
        $repo_link = "http://goldadx.com/createrepo?app_no=" . $this->app_no . "&package_name=" . $this->package_name;
        $res = Http::get($repo_link);
        $repo_response = $res->getBody()->getContents();
        $task->repo_link = $repo_response;


        // phase //
        if ($task->assign_person == null) {
            $task->phase = null;
        } else {
            if ($task->User->designation == 'designer') {
                $task->phase = "designing";
            } elseif ($task->User->designation == 'developer') {
                $task->phase = "developing";
            } else {
                $task->phase = null;
            }
        }


        // assign_aso  and  aso_status //
        if ($this->assign_person !== null) {
            if ($task->User->designation == 'developer') {
                $aso_user = User::where('designation','ASO')->get();
                $task->assign_aso = $aso_user[0]->id;
                $task->aso_status = 'pending';
            }
        }

        $task->status = 'pending';
        $task->console_app = $this->console_app;
        $task->description = $this->description;
        $task->deadline = $this->deadline;
        $task->priority = 'low';

        $task->save();

        // send notification assign user //
        if ($this->assign_person !== null) {
            $user = User::where('id',$this->assign_person)->get();
            $notification = $user[0];
            $task_details = [
                'task_id' => $task->id,
                'app_no'=>$task->app_no,
                'task_title' => $task->title,
                'assign_person' => $task->assign_person,
            ];
            $notification->notify(new assignPersonNotification($task_details,$auth_user));
        }

        // call event
        // event(new UserEvent($auth_user));

        return $task;

    }
}




