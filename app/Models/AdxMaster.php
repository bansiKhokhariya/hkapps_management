<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class AdxMaster extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'adx_master';
    protected $fillable = ['adx_register_company', 'adx', 'adx_share', 'type'];
    protected $dates = ['deleted_at'];
    protected static $logName = 'Adx Master';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} AdxMaster - ID:{$this->id}, adx:{$this->adx}";
    }
}
