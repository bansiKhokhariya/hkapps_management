<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class AdPlacement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ad_placement';
    protected $guarded = [];
    protected $appends = ['platform_name'];

    public function platform()
    {
        return $this->belongsTo(PlatForm::class);
    }

    public function getPlatformNameAttribute()
    {
        return $this->platform->platform_name;
    }

}
