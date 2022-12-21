<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DummyPackage extends Model
{
    use HasFactory;
    protected $table ='dummy_packages';
    protected $guarded = [];
}
