<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Time extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'time';
    protected $fillable = ['user_id', 'task_id', 'start_time','stop_date', 'time', 'end_date','is_started','assigned_date'];
}
