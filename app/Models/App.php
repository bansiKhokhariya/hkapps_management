<?php

namespace App\Models;

use App\Filters\AppFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Http;

class App extends Model
{
    use HasFactory , SoftDeletes;
    protected $connection = 'mysql4';

    protected $table = 'apps';
    protected $fillable = ['package_name','title','icon','developer'];

    protected static $logName = 'App';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} App - ID:{$this->id}, packageName:{$this->package_name}";
    }


    public function scopeFilter($query)
    {
        return resolve(AppFilters::class)->apply($query);
    }

    public function appDetails()
    {

//        $app_details = AppDetails::where('app_packageName', $this->app_packageName)->first();
//        return $app_details;

        $app_details_link = "https://lplciltdwh6kd6qjl4ytd6tzoq0iaumr.lambda-url.us-east-1.on.aws/?id=" . $this->package_name;
        $res = Http::get($app_details_link);
        if ($res->status() == 200) {
            $repo_response = $res->getBody()->getContents();
            $value = json_decode($repo_response);
        }

    }

    public function TotalRequestCount()
    {
        $get_api_keylist = ApikeyList::where('apikey_packageName', $this->package_name)->sum('apikey_request');
        $totalRequest = (int)$get_api_keylist;
        return $totalRequest;
    }
}
