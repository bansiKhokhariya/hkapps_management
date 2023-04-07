<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class DefaultTodo extends Model
{
    use HasFactory , LogsActivity;
    protected $table = 'default_todo';
    protected $guarded = [];
    protected  $connection ='mysql';
    protected static $logName = 'Default Todo';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} Default Todo - ID:{$this->id}, TodoName:{$this->ads_master}";
    }

}
