<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class AdsMaster extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'ads_master';
    protected $fillable = ['cid', 'tel_id', 'ads_master'];
    protected $dates = ['deleted_at'];
    protected static $logName = 'Ads Master';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} AdsMaster - ID:{$this->id}, adsMaster:{$this->ads_master}";
    }
}
