<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class TodoList extends Model
{
    use HasFactory, LogsActivity;

    protected $connection = 'mysql';
    protected static $logName = 'Todo List';
    protected $table = 'todo_list';
    protected $fillable = ['todoList', 'task_id', 'completed'];

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} Todo - ID:{$this->id}, TodoName:{$this->adx}";
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

}
