<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartyMaster extends Model
{
    use HasFactory,SoftDeletes;
    protected $table ='party_master';
    protected $fillable = ['company_name','party'];
    protected $dates = ['deleted_at'];
}
