<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommanMaster extends Model
{
    use HasFactory , SoftDeletes;
    protected $table = 'comman_master';
    protected $guarded = [];
//    protected $connection = 'mysql';


}
