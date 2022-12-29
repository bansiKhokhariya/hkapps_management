<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdxMaster extends Model
{
    use HasFactory,SoftDeletes;
    protected $table ='adx_master';
    protected $fillable = ['adx_register_company','adx','adx_share','type'];
    protected $dates = ['deleted_at'];
}
