<?php

namespace App\Console\Commands;

use App\Models\AllApps;
use App\Models\AppDetails;
use App\Models\User;
use App\Notifications\LiveAppNotification;
use App\Notifications\RemoveAppNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\App;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CheckAppStatusCron extends Command
{

    protected $signature = 'CheckAppStatus:cron';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function AppDetails($packageName)
    {

        $app_details_link = "https://lplciltdwh6kd6qjl4ytd6tzoq0iaumr.lambda-url.us-east-1.on.aws/?id=" . $packageName;
        $res = Http::get($app_details_link);
        if ($res->status() == 200) {

            $repo_response = $res->getBody()->getContents();
            $value = json_decode($repo_response);

            // **** update app_details **** //
            $get_app_details = AppDetails::where('app_packageName', $packageName)->first();
            if (!$get_app_details) {
                $appDetails = new AppDetails();
                $appDetails->app_packageName = $packageName;
                $appDetails->description = $value->description;
                $appDetails->descriptionHTML = $value->descriptionHTML;
                $appDetails->summary = $value->summary;
                $appDetails->installs = $value->installs;
                $appDetails->minInstalls = $value->minInstalls;
                $appDetails->realInstalls = $value->realInstalls;
                $appDetails->score = $value->score;
                $appDetails->ratings = $value->ratings;
                $appDetails->reviews = $value->reviews;
                $appDetails->histogram = json_encode($value->histogram);
                $appDetails->price = $value->price;
                $appDetails->free = $value->free;
                $appDetails->currency = $value->currency;
                $appDetails->sale = $value->sale;
                $appDetails->saleTime = $value->saleTime;
                $appDetails->originalPrice = $value->originalPrice;
                $appDetails->saleText = $value->saleText;
                $appDetails->offersIAP = $value->offersIAP;
                $appDetails->inAppProductPrice = $value->inAppProductPrice;
                $appDetails->developer = $value->developer;
                $appDetails->developerId = $value->developerId;
                $appDetails->developerEmail = $value->developerEmail;
                $appDetails->developerWebsite = $value->developerWebsite;
                $appDetails->developerAddress = $value->developerAddress;
                $appDetails->genre = $value->genre;
                $appDetails->genreId = $value->genreId;
                $appDetails->headerImage = $value->headerImage;
                $appDetails->screenshots = json_encode($value->screenshots);
                $appDetails->video = $value->video;
                $appDetails->videoImage = $value->videoImage;
                $appDetails->contentRating = $value->contentRating;
                $appDetails->contentRatingDescription = $value->contentRatingDescription;
                $appDetails->adSupported = $value->adSupported;
                $appDetails->containsAds = $value->containsAds;
                $appDetails->released = $value->released;
                $appDetails->updated = $value->updated;
                $appDetails->version = $value->version;
                $appDetails->recentChanges = $value->recentChanges;
                $appDetails->recentChangesHTML = $value->recentChangesHTML;
                $appDetails->comments = json_encode($value->comments);
                $appDetails->url = $value->url;
                $appDetails->status = 'live';
                $appDetails->save();
            }

            // **** //

            // **** update all_apps **** //

            $redis = Redis::connection('RedisApp2');
            $response = $redis->get($packageName);
            $app_res_redis = json_decode($response);

            $getAllApps = AllApps::where('app_packageName', $packageName)->first();
            $allApp = AllApps::find($getAllApps->id);
            $allApp->app_logo = $value->icon;
            $allApp->app_name = $value->title;
            if ($app_res_redis->STATUS == 'true') {
                $allApp->app_accountName = $app_res_redis->APP_SETTINGS->app_accountName;
            }
            $allApp->app_privacyPolicyLink = $value->privacyPolicy;

            $allApp->save();

            // **** //


        }
    }

    public function handle()
    {

        \Log::info("check App status cron!");
        $allApps = AllApps::get();

        if ($allApps->count() > 0) {
            foreach ($allApps as $allApp) {

                $app_details_link = "https://play.google.com/store/apps/details?id=" . $allApp->app_packageName;
                $res = Http::get($app_details_link);
                if ($res->status() == 200) {
                    $get_app = AllApps::where('app_packageName', $allApp->app_packageName)->first();
                    if ($get_app) {

                        if ($get_app->status == 'live') {

                            $get_app->status = 'live';
                            $get_app->save();

                            // save app details //
                            $this->AppDetails($allApp->app_packageName);
                            //


                        } else {
                            // send live app notification //
                            $user = User::where('roles', 'super_admin')->first();
                            $auth_user = User::find($user->id);
                            $user_notifiy = User::where('designation', 'superadmin')->get();
                            $app_details = [
                                'Package Name' => $get_app->app_packageName,
                                'App Name' => $get_app->app_name,
                                'Icon' => $get_app->app_logo,
                            ];
                            foreach ($user_notifiy as $notification) {
                                $notification->notify(new LiveAppNotification($app_details, $auth_user));
                            }
                            //****** //

                            $get_app->status = 'live';
                            $get_app->save();

                            // save app details //
                            $this->AppDetails($allApp->app_packageName);
                            //

                        }


                    }
                } else {
                    $get_app = AllApps::where('app_packageName', $allApp->app_packageName)->where('status', 'live')->first();
                    if ($get_app) {

                        // \Log::info($get_app->id);
                        // send removed app notification //
                        $user = User::where('roles', 'super_admin')->first();
                        $auth_user = User::find($user->id);
                        $user_notifiy = User::where('designation', 'superadmin')->get();
//                        $notification = $user;
                        $app_details = [
                            'Package Name' => $get_app->app_packageName,
                            'App Name' => $get_app->app_name,
                            'Icon' => $get_app->app_logo,
                        ];
                        foreach ($user_notifiy as $notification) {
                            $notification->notify(new RemoveAppNotification($app_details, $auth_user));
                        }
                        //****** //

                        $get_app->status = 'removed';
                        $get_app->save();

                    }
                }

                // ************* save ads file ************ //

                $app_details = AppDetails::where('app_packageName', $allApp->app_packageName)->first();
                $developerUrl = $app_details->developerWebsite;

                if ($developerUrl) {
                    $checkDeveloperUrl = Str::contains($developerUrl, 'ads.txt');

                    if ($checkDeveloperUrl == true) {
                        $txtRes = Http::get($developerUrl);
                        if ($txtRes->status() == 200) {
                            Storage::disk('public')->put('AdsFile/' . $allApp->app_packageName, $txtRes);
                        }
                    } else {
                        $txtRes = Http::get($developerUrl . '/ads.txt');
                        if ($txtRes->status() == 200) {
                            Storage::disk('public')->put('AdsFile/' . $allApp->app_packageName, $txtRes);
                        }

                    }
                }

                // ************ //

            }
        }

        \Log::info("check App status cron run succesfully!");
    }
}
