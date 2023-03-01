<?php

namespace App\Http\Controllers\API;

use App\Events\UserEvent;
use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\CompanyMaster;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;


class AppsController extends Controller
{
    public function index(){

        $companyUser = Auth::user()->company_master_id;
        $companyMaster = CompanyMaster::where('id',$companyUser)->first();

        if (!$companyUser) {
            $app = App::filter()->latest()->paginate(9);
        } else {
            $app = App::where('app_accountName', $companyMaster->company)->filter()->latest()->paginate(9);
        }

        return response()->json($app);

    }

    public function store(Request $request){

        $app = new App();
        $app->title = $request->title;
        $app->package_name = $request->package_name;
        $app->icon = $request->icon;
        $app->developer = $request->developer;
        $app->save();

        return response()->json($app);

    }

    public function fetchAppData($package_name){

        $app_link = "https://lplciltdwh6kd6qjl4ytd6tzoq0iaumr.lambda-url.us-east-1.on.aws/?id=" . $package_name;

        $res = Http::get($app_link);

        if($res->status() == 200){

//            // for event
//            $id = Auth::user()->id;
//            $auth_user = User::find($id);
//            //

            $app_response = json_decode($res->getBody()->getContents());

            $redis = Redis::connection('RedisApp6');
            $response = $redis->get($package_name);
            $app_res_redis =  json_decode($response);


            $get_app = App::where('package_name',$package_name)->first();
            if(!($get_app)){
                $app = new App();
                $app->title = $app_response->title;
                $app->package_name = $package_name;
                $app->icon = $app_response->icon;
                $app->developer = $app_response->developer;
                $app->app_accountName = $app_res_redis->APP_SETTINGS->app_accountName;
                $app->save();

                //event call
                // event(new UserEvent($auth_user));

                return response()->json($app);
            }

        }else{

            $app_response = $res->getBody()->getContents();
            return response()->json($app_response,500);
        }

    }

    public function getPackageList(){

        $app_link = "https://webcreon.com/direct/getlist";
        $res = Http::get($app_link);
        return $res;

    }

    public function getCurrentPackage($package_name){

        $app_link = "https://webcreon.com/direct/getcurrent?pkg=". $package_name;;
        $res = Http::get($app_link);
        return $res;
    }

    public function getDB6Data($package_name){

        $redis = Redis::connection('RedisApp6');
        $response = $redis->get($package_name);
        return json_decode($response);

    }

    public function setData(Request $request){

        $redis = Redis::connection('RedisApp6');
        $package_name = $request->package_name;
        $response = $request->jsonData;

        $redis->set($package_name, $response);

        return 'Data set succesfully!';

    }

    public function getDB6AllData(){

        $redis = Redis::connection('RedisApp6');
        $response = $redis->keys('*');
        return response()->json($response);

    }

}
