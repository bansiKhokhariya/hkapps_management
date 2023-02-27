<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SpyAppResource;
use App\Models\SpyApps;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Nelexa\GPlay\Model\GoogleImage;

class SpyAppsController extends Controller
{

    public function getSpyApps()
    {

        $getSpyApps = SpyApps::latest()->simplePaginate(27);
        return SpyAppResource::collection($getSpyApps);

    }


    public function getSpyApp($packageName){

        $getSpyApp = SpyApps::where('packageName',$packageName)->get();
        return SpyAppResource::collection($getSpyApp);

    }

    public function saveSpyApps()
    {

        $gPlay = new \Nelexa\GPlay\GPlayApps();
        $newApp = $gPlay->getNewApps();

        foreach ($newApp as $app) {


            $screenshots = array_map(
                static function (GoogleImage $googleImage) {
                    return $googleImage->getUrl();
                },
                $app->screenshots
            );


            $getSpyApps = SpyApps::where('packageName', $app->id)->first();
            if (!$getSpyApps) {
                $spyApp = new SpyApps();
                $spyApp->packageName = $app->id;
                $spyApp->url = $app->getUrl();
                $spyApp->locale = $app->locale;
                $spyApp->country = $app->country;
                $spyApp->name = $app->name;
                $spyApp->description = $app->description;
                $spyApp->developerName = $app->developerName;
                $spyApp->icon = $app->icon;
                $spyApp->screenshots = json_encode($screenshots);
                $spyApp->score = $app->score;
                $spyApp->priceText = $app->priceText;
                $spyApp->installsText = $app->installsText;
                $spyApp->version = $app->appVersion;
                $spyApp->category = $app->category->id;
                $spyApp->save();
            }
        }
        return 'all new spy app save!';

    }

    public function saveSpyApp(Request $request)
    {


        $gPlay = new \Nelexa\GPlay\GPlayApps();

        $checkApp = $gPlay->existsApp($request->packageName);

        if ($checkApp > 0) {

            $appInfo = $gPlay->getAppInfo($request->packageName);


            return $appInfo;

            $screenshots = array_map(
                static function (GoogleImage $googleImage) {
                    return $googleImage->getUrl();
                },
                $appInfo->screenshots
            );


            $getSpyApps = SpyApps::where('packageName', $request->packageName)->first();
            if (!$getSpyApps) {
                $spyApp = new SpyApps();
                $spyApp->packageName = $request->packageName;
                $spyApp->url = $appInfo->getUrl();
                $spyApp->locale = $appInfo->locale;
                $spyApp->country = $appInfo->country;
                $spyApp->name = $appInfo->name;
                $spyApp->description = $appInfo->description;
                $spyApp->developerName = $appInfo->developer->getName();
                $spyApp->screenshots = json_encode($screenshots);
                $spyApp->icon = $appInfo->icon->getUrl();
                $spyApp->score = $appInfo->score;
                $spyApp->priceText = $appInfo->priceText;
                $spyApp->installsText = $appInfo->installsText;
                $spyApp->released = $appInfo->released;
                $spyApp->updated = $appInfo->updated;
                $spyApp->version = $appInfo->appVersion;
                $spyApp->category = $appInfo->category->id;
                $spyApp->save();

                return $spyApp;
            }
        } else {

            return Response::json([
                'message' => 'this app is not exists in google play store'
            ], 404);

        }


    }




}
