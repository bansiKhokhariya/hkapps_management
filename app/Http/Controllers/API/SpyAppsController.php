<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SpyAppResource;
use App\Models\SpyApps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Nelexa\GPlay\Model\GoogleImage;

class SpyAppsController extends Controller
{

    public function getSpyApps()
    {

        $getSpyApps = SpyApps::latest()->get();
        return SpyAppResource::collection($getSpyApps);

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
