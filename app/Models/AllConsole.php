<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class AllConsole extends Model
{
    use HasFactory,LogsActivity;
    protected $connection = 'mysql4';
    protected $table = 'all_console';
    protected $guarded = [];
    protected static $logName = 'Console';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} Console - ID:{$this->id}, ConsoleName:{$this->consoleName}";
    }
    public function manageBy()
    {
        return $this->belongsTo(User::class);
    }

}
