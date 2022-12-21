<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PlatForm;

class TestAdPlacement extends Model
{
    use HasFactory;

    protected $table = 'test_ad_placement';
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
