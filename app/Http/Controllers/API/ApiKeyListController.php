<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateApiKeyListRequest;
use App\Http\Requests\UpdateApiKeyListRequest;
use App\Http\Resources\ApiKeyListResource;
use App\Models\AllApps;
use App\Models\ApikeyList;
use Illuminate\Http\Request;
use App\Events\RedisDataEvent;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;

class ApiKeyListController extends Controller
{
    public function index()
    {
        $apikey_list = ApikeyList::filter()->latest()->get();
        return ApiKeyListResource::collection($apikey_list);
    }

    public function store(CreateApiKeyListRequest $request)
    {
        return ApiKeyListResource::make($request->persist());
    }

    public function show(ApikeyList $apikey_list)
    {
        return ApiKeyListResource::make($apikey_list);
    }

    public function update(UpdateApiKeyListRequest $request, ApikeyList $apikey_list)
    {
        return ApiKeyListResource::make($request->persist($apikey_list));
    }

    public function destroy(ApikeyList $apikey_list)
    {
        $apikey_list->delete();

        // call event
        // event(new RedisDataEvent());

        return response('ApikeyList Deleted Successfully');
    }

    public function assignApiKey(Request $request)
    {

        $package_name = $request->package_name;
        $apikey = $request->apikey;

        $allApps = AllApps::where('app_packageName', $package_name)->first();

        $apikey_json = $allApps->app_apikey;
        $allApps = AllApps::find($allApps->id);
        $old_apikey = $allApps->app_apikey;
        $new_apikey = $request->apikey;
        if ($allApps->app_apikey) {
            $allApps->app_apikey = $old_apikey . ',' . $new_apikey;
        } else {
            $allApps->app_apikey = $request->apikey;
        }
        $allApps->save();

        // ***************** view app response json ******************** //
        $getApp = new AllApps();
        $result = $getApp->viewResponse($package_name, $apikey);

        $redis = Redis::connection('RedisApp10');
        $redis->set($package_name, json_encode($result));


        // delete apikey list //
        $apikeyList = ApikeyList::where('apikey_packageName', $package_name)->where('apikey_text', $apikey)->first();
        $apikeyList->forceDelete();

        // call event
        // event(new RedisDataEvent());

        return response()->json('apikey assign succesfully!');

    }

    public function getRedisApiKey()
    {
        $redis3 = Redis::connection('RedisApp3');
        $response3 = $redis3->keys("*");
        $getValue = $redis3->mget($response3);
        $apikeyList = array_map(function ($value) {
            return json_decode($value);
        }, $getValue);

        $apikey = Arr::where($apikeyList, function ($value, $key)  {
            if(isset($value->status)){
                return $value->status == 'unauthorized';
            }
        });

        return array_values($apikey);

    }

    public function setRedisApiKey(Request $request){

        $redis3 = Redis::connection('RedisApp3');

        $package_name = $request->package_name;
        $response = $request->jsonData;

        $redis3->set($package_name, $response);

        return 'Data set succesfully!';

    }


}
