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

    public function getSpyApp($packageName)
    {

        $getSpyApp = SpyApps::where('packageName', $packageName)->get();
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

    public function appBrowse(Request $request)
    {
        $mode = $request->query('mode');
        $available = $request->query('available');
        $query = $request->query('query');
        $query_short = $request->query('query_short');
        $query_description = $request->query('query_description');
        $revenue = $request->query('revenue');
        $downloads = $request->query('downloads');
        $installs = $request->query('installs');
        $ipd = $request->query('ipd');
        $size = $request->query('size');
        $store = $request->query('store');
        $type = $request->query('type');
        $released = $request->query('released');
        $ratings = $request->query('ratings');
        $reviews = $request->query('reviews');
        $updates = $request->query('updates');
        $dev = $request->query('dev');
        $similarapp = $request->query('similarapp');
        $builder = $request->query('builder');
        $collection = $request->query('collection');
        $address_country = $request->query('address_country');
        $limit = $request->query('limit');
        $order = $request->query('order');
        $dir = $request->query('dir');
        $bucket = $request->query('bucket');
        $bucket_date = $request->query('bucket_date');
        $wl = $request->query('wl');
        $inapp = $request->query('inapp');
        $creatives = $request->query('creatives');
        $website = $request->query('website');
        $country = $request->query('country');
        $category = $request->query('category');
        $storepass = $request->query('storepass');
        $wearos = $request->query('wearos');


        $getBrowse = Http::withHeaders([
            'accept' => "application/json",
            'API-KEY' => config('global.API-KEY')
        ])->get('https://appstorespy.com/browse.json', [
            'mode' => $mode,
            'available' => $available,
            'query' => $query,
            'query_short' => $query_short,
            'query_description' => $query_description,
            'revenue' => $revenue,
            'downloads' => $downloads,
            'installs' => $installs,
            'ipd' => $ipd,
            'size' => $size,
            'store' => $store,
            'type' => $type,
            'released' => $released,
            'ratings' => $ratings,
            'reviews' => $reviews,
            'updates' => $updates,
            'dev' => $dev,
            'similarapp' => $similarapp,
            'builder' => $builder,
            'address_country' => $address_country,
            'limit' => $limit,
            'order' => $order,
            'dir' => $dir,
            'bucket' => $bucket,
            'bucket_date' => $bucket_date,
            'wl' => $wl,
            'inapp' => $inapp,
            'creatives' => $creatives,
            'website' => $website,
            'collection' => $collection,
            'country' => $country,
            'category' => $category,
            'storepass' => $storepass,
            'wearos' => $wearos,
        ]);

        $response = $getBrowse->json();

        if (array_key_exists('errors', $response)) {
          return response($response , 422);
        }

        return $response;

    }


}
