<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class AppDetails extends Model
{
    use HasFactory , LogsActivity;
    protected $table = 'app_details';
    protected $guarded = [];

    protected static $logName = 'App Details';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} App Details - ID:{$this->id}";
    }

}
