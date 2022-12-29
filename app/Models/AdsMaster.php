<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdsMaster extends Model
{
    use HasFactory,SoftDeletes;
    protected $table ='ads_master';
    protected $fillable = ['cid','tel_id','ads_master'];
    protected $dates = ['deleted_at'];
}
