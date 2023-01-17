<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;


class User extends Authenticatable
{

    use Notifiable;
    use HasApiTokens, HasFactory, HasRoles, SoftDeletes, LogsActivity;

    protected $appends = ['Task', 'TotalCount', 'PendingCount', 'ReWorkingCount', 'WorkingCount', 'doneCount'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    const designer = 'designer';
    const developer = 'developer';
    const tester = 'tester';
    const aso = 'ASO';
    const admin = 'admin';
    const production = 'production';
    const superadmin = 'Super Admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'roles',
        'designation'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static $logName = 'User';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} User - ID:{$this->id}, UserName:{$this->name}";
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class)->withTimestamps();
    }

    public function companyMaster()
    {
        return $this->belongsTo(CompanyMaster::class)->withTimestamps();
    }

    public function role()
    {
        $role = Role::where('name', $this->roles)->with('permissions')->first();
        return $role;
    }

    public function receivesBroadcastNotificationsOn()
    {
        return 'pankaj.' . $this->id;
    }


    public function getTaskAttribute()
    {
        $task = Task::where('assign_person', $this->id)->get();
        return $task;
    }

    public function getTotalCountAttribute()
    {
        $task = Task::where('assign_person', $this->id)->count();
        return $task;
    }

    public function getPendingCountAttribute()
    {
        $task = Task::where('assign_person', $this->id)->where('status', 'pending')->count();
        return $task;
    }

    public function getWorkingCountAttribute()
    {
        $task = Task::where('assign_person', $this->id)->where('status', 'working')->count();
        return $task;
    }

    public function getReWorkingCountAttribute()
    {
        $task = Task::where('assign_person', $this->id)->where('status', 're-working')->count();
        return $task;
    }

    public function getdoneCountAttribute()
    {
        $task = Task::where('assign_person', $this->id)->where('status', 'done')->count();
        return $task;
    }

}


