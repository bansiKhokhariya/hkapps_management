<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GitHubToken extends Model
{
    use HasFactory;
    protected $table = 'github_token';
    protected $connection = 'mysql';
    protected $guarded =[];
}
