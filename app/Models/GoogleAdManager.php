<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class GoogleAdManager extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'google_admanager';
    protected $guarded = [];
    protected static $logName = 'Google Ad Manager';

    protected $connection = 'mysql';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} Google Ad Manager - ID:{$this->id}";
    }
}
