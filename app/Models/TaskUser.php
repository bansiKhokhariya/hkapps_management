<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskUser extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "task_user";
    protected $fillable = ['task_id','user_id','status'];

    public function task(){
        return $this->belongsTo(Task::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
