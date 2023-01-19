<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class PlatForm extends Model
{
    use HasFactory, SoftDeletes , LogsActivity;
    protected $connection = 'mysql4';

    protected $table = 'platform';
    protected $fillable = ['logo', 'platform_name', 'ad_format', 'status'];
    protected static $logName = 'PlatForm';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} PlatForm - ID:{$this->id}, platformName:{$this->platform_name}";
    }
    public function companyMaster()
    {
        return $this->belongsTo(CompanyMaster::class);
    }
}
