<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllAppsHistory extends Model
{
    use HasFactory;

    protected $table = 'all_apps_history';
    protected $connection = 'mysql4';
    protected $guarded = [];
}
