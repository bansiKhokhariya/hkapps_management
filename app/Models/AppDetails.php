<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class AppDetails extends Model
{
    use HasFactory;
    protected $connection = 'mysql4';

    protected $table = 'app_details';
    protected $guarded = [];



}
