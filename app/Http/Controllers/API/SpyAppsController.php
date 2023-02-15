<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SpyApps;
use Illuminate\Http\Request;
use Nelexa\GPlay\Model\GoogleImage;

class SpyAppsController extends Controller
{

    public function getSpyApps()
    {

        $getSpyApps = SpyApps::latest()->get();
        return $getSpyApps;

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

}
