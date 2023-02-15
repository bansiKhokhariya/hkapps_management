<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpyApps extends Model
{
    use HasFactory;
    protected $connection = 'mysql4';
    protected $table = 'spy_apps';
}
