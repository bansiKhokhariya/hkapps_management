<?php

namespace App\Http\Controllers\API;

use App\Events\UserEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\WebCreon2Resource;
use App\Models\AllApps;
use App\Models\App;
use App\Models\AppDetails;
use App\Models\CompanyMaster;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;


class AppsController extends Controller
{
    public function index()
    {

        $companyUser = Auth::user()->company_master_id;
        $companyMaster = CompanyMaster::where('id', $companyUser)->first();

        if (!$companyUser) {
            $app = AllApps::where('status','live')->filter()->latest()->paginate(9);
        } else {
            $app = AllApps::where('status','live')->where('app_accountName', $companyMaster->company)->filter()->latest()->paginate(9);
        }

//        return response()->json($app);
        return WebCreon2Resource::collection($app);

    }

    public function store(Request $request)
    {

        $app = new AllApps();
        $app->app_name = $request->app_name;
        $app->app_packageName = $request->app_packageName;
        $app->app_logo = $request->app_logo;
        $app->developer = $request->developer;
        $app->save();

        return response()->json($app);

    }

    public function fetchAppData($package_name)
    {

        $app_link = "https://lplciltdwh6kd6qjl4ytd6tzoq0iaumr.lambda-url.us-east-1.on.aws/?id=" . $package_name;


        $res = Http::get($app_link);

        if ($res->status() == 200) {

//            // for event
//            $id = Auth::user()->id;
//            $auth_user = User::find($id);
//            //

            $app_response = json_decode($res->getBody()->getContents());


            $redis = Redis::connection('RedisApp2');
            $response = $redis->get($package_name);
            $app_res_redis = json_decode($response);


            $get_app = App::where('package_name', $package_name)->first();
            if (!($get_app)) {
                $app = new AllApps();
                $app->app_name = $app_response->app_name;
                $app->app_packageName = $package_name;
                $app->app_logo = $app_response->app_logo;
//                $app->developer = $app_response->developer;
                $app->app_accountName = $app_res_redis->APP_SETTINGS->app_accountName;
                $app->status = 'live';
                $app->save();

                //event call
                // event(new UserEvent($auth_user));

                $get_app_details = AppDetails::where('app_packageName', $package_name)->first();
                if(!$get_app_details){
                    $appDetails = new AppDetails();
                    $appDetails->app_packageName = $package_name;
                    $appDetails->description = $app_response->description;
                    $appDetails->descriptionHTML = $app_response->descriptionHTML;
                    $appDetails->summary = $app_response->summary;
                    $appDetails->installs = $app_response->installs;
                    $appDetails->minInstalls = $app_response->minInstalls;
                    $appDetails->realInstalls = $app_response->realInstalls;
                    $appDetails->score = $app_response->score;
                    $appDetails->ratings = $app_response->ratings;
                    $appDetails->reviews = $app_response->reviews;
                    $appDetails->histogram = json_encode($app_response->histogram);
                    $appDetails->price = $app_response->price;
                    $appDetails->free = $app_response->free;
                    $appDetails->currency = $app_response->currency;
                    $appDetails->sale = $app_response->sale;
                    $appDetails->saleTime = $app_response->saleTime;
                    $appDetails->originalPrice = $app_response->originalPrice;
                    $appDetails->saleText = $app_response->saleText;
                    $appDetails->offersIAP = $app_response->offersIAP;
                    $appDetails->inAppProductPrice = $app_response->inAppProductPrice;
                    $appDetails->developer = $app_response->developer;
                    $appDetails->developerId = $app_response->developerId;
                    $appDetails->developerEmail = $app_response->developerEmail;
                    $appDetails->developerWebsite = $app_response->developerWebsite;
                    $appDetails->developerAddress = $app_response->developerAddress;
                    $appDetails->genre = $app_response->genre;
                    $appDetails->genreId = $app_response->genreId;
                    $appDetails->headerImage = $app_response->headerImage;
                    $appDetails->screenshots = json_encode($app_response->screenshots);
                    $appDetails->video = $app_response->video;
                    $appDetails->videoImage = $app_response->videoImage;
                    $appDetails->contentRating = $app_response->contentRating;
                    $appDetails->contentRatingDescription = $app_response->contentRatingDescription;
                    $appDetails->adSupported = $app_response->adSupported;
                    $appDetails->containsAds = $app_response->containsAds;
                    $appDetails->released = $app_response->released;
                    $appDetails->updated = $app_response->updated;
                    $appDetails->version = $app_response->version;
                    $appDetails->recentChanges = $app_response->recentChanges;
                    $appDetails->recentChangesHTML = $app_response->recentChangesHTML;
                    $appDetails->comments = json_encode($app_response->comments);
                    $appDetails->url = $app_response->url;
                    $appDetails->status = 'live';
                    $appDetails->save();
                }


                return response()->json($app);
            }

        } else {

            $app_response = $res->getBody()->getContents();
            return response()->json($app_response, 500);
        }

    }

    public function getPackageList()
    {

        $app_link = "https://webcreon.com/direct/getlist";
        $res = Http::get($app_link);
        return $res;

    }

    public function getCurrentPackage($package_name)
    {

        $app_link = "https://webcreon.com/direct/getcurrent?pkg=" . $package_name;;
        $res = Http::get($app_link);
        return $res;
    }

    public function getDB6Data($package_name)
    {

        $redis = Redis::connection('RedisApp6');
        $response = $redis->get($package_name);
        return json_decode($response);

    }

    public function getDB2Data($package_name)
    {

        $redis = Redis::connection('RedisApp2');
        $response = $redis->get($package_name);
        return json_decode($response);

    }

    public function setData(Request $request)
    {

        $redis = Redis::connection('RedisApp6');
        $package_name = $request->package_name;
        $response = $request->jsonData;

        $redis->set($package_name, $response);

        return 'Data set succesfully!';

    }

    public function getDB6AllData()
    {

        $redis = Redis::connection('RedisApp6');
        $response = $redis->keys('*');
        return response()->json($response);

    }

    public function CopyDataFromTo(Request $request)
    {

        $from = $request->from;
        $to = $request->to;

        if ($request->from && $request->to) {
            $redis = Redis::connection('RedisApp6');
            $fromReponse = $redis->get($from);

            $redis->set($to, $fromReponse);

            return 'data copy and paste succesfully';
        }
        return response()->json('please enter package name', '422');

    }

    public function getWebCreonPackage()
    {

        $getWebCreonPackage = AllApps::where('status','live')->pluck('app_packageName');
        return $getWebCreonPackage;

    }

    public function webCreon2List()
    {
        $app = AllApps::where('status','live')->get();
//        return response()->json($app);
        return WebCreon2Resource::collection($app);
    }

    public function getAppInfoWebCreon2($packageName){

        $adplacement = AllApps::where('app_packageName', $packageName)->where('status','live')->get();
        return WebCreon2Resource::collection($adplacement);

    }


}
