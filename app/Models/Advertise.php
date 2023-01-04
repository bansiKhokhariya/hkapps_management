<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Advertise extends Model
{
    use HasFactory, SoftDeletes;

    protected static $logName = 'Advertise';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} Advertise - ID:{$this->id}, appName:{$this->app_name}";
    }

    protected $table = 'advertise';
    protected $fillable = ['ad_id', 'app_name', 'app_packageName', 'app_logo', 'app_banner', 'app_shortDecription', 'app_buttonName', 'app_rating', 'app_download', 'app_AdFormat'];


}
