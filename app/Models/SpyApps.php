<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SpyApps extends Model
{
    use HasFactory;

    protected $connection = 'mysql4';
    protected $table = 'spy_apps';


    public function getSpyAppDetails()
    {
        $getSpyAppDetails = SpyAppDetails::where('packageName', $this->packageName)->latest('id')->first();
        return $getSpyAppDetails;
    }

    public function getInstalls($packageName)
    {
        $getSpyApp = SpyAppDetails::where('packageName', $packageName)->select('daily_installs', 'created_at')->get();

        return $getSpyApp;
    }


}
