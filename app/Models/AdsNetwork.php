<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class AdsNetwork extends Model
{
    use HasFactory ,LogsActivity;
    protected $table = 'ads_network';
    protected $guarded = [];
    protected  $connection ='mysql';
    protected static $logName = 'Ads Network';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} AdsNetwork - ID:{$this->id}";
    }

}
