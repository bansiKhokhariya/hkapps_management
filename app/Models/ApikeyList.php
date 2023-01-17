<?php

namespace App\Models;

use App\Filters\ApikeyFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ApikeyList extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'apikey_list';
    protected $guarded = [];

    protected static $logName = 'Apikey list';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} apikey_list - ID:{$this->id}, app_apikey:{$this->apikey_text}";
    }

    public function scopeFilter($query)
    {
        return resolve(ApikeyFilters::class)->apply($query);
    }
    public function companyMaster()
    {
        return $this->belongsTo(CompanyMaster::class);
    }
}
