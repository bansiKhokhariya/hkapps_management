<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Telegram extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'telegram';
    protected $guarded = [];

    protected static $logName = 'Telegram';

    protected $connection = 'mysql';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} Telegram token";
    }
}
