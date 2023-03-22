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
    use HasFactory, SoftDeletes, LogsActivity;

    protected $dates = ['deleted_at'];
    protected $table = 'task';
    protected $guarded = [];


    protected static $logName = 'Task';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} Task - ID:{$this->id}, Title:{$this->title}";
    }


}





