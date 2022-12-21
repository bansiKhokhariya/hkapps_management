<?php

namespace App\Models;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Spatie\Activitylog\Traits\LogsActivity;

class Task extends Model

{
    use Notifiable;
    use HasFactory, SoftDeletes , LogsActivity;

    protected $dates = ['deleted_at'];
    protected $table = 'task';
    protected $fillable = ['id', 'app_no', 'title', 'package_name', 'reference_app', 'repo_link', 'attchments', 'attchments_link', 'console_app', 'assigned_people', 'assign_person', 'phase', 'description', 'status', 'assigned_date', 'completed_date', 'priority'];

    const pending = 'pending';
    const working = 'working';
    const ready_testing = 'ready_testing';
    const done = 'done';
    const re_working = 're-working';

    const designing = 'designing';
    const developing = 'developing';
    const testing_designing = 'testing_designing';
    const testing_developing = 'testing_developing';
    const production = 'production';

    const low = 'low';
    const medium = 'medium';
    const high = 'high';


    protected static $logName = 'Task';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} Task - ID:{$this->id}, Title:{$this->title}";
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'assign_person', 'id');
    }

    public function assign_person()
    {
        return $this->belongsTo(User::class, 'assign_person', 'id');
    }

    public function person()
    {
        return $this->belongsTo(User::class, 'assign_person', 'id')->where('designation', 'tester')->get();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'task_user',
            'task_id', 'user_id')->withTimestamps();
    }

    public function assigned_people()
    {
        return $this->belongsToMany(User::class, 'task_user',
            'task_id', 'user_id');
    }

    public function user_get()
    {
        return $this->belongsToMany(User::class, 'task_user',
            'task_id', 'user_id')->get();
    }

    public function getphase()
    {

        $get_task = Task::where('id', $this->id)->get();
        return $get_task[0]->phase;

    }


}





