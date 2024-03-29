<?php

namespace App\Console\Commands;

use App\Models\AllApps;
use App\Models\AppDetails;
use App\Models\Setting;
use App\Models\TestAllApp;
use App\Models\User;
use App\Notifications\RemoveAppNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class AppDetailsUpadateCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AppDetailsUpdate:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $appDetailsUpdate = Setting::where('cron', 'AppDetailsUpdate')->first();
        if($appDetailsUpdate->infinity == 1){
            \Log::info("App Details update cron!");

            $allApps = AllApps::get();

            if ($allApps->count() > 0) {
                foreach ($allApps as $allApp) {
                    $app_details_link = "https://lplciltdwh6kd6qjl4ytd6tzoq0iaumr.lambda-url.us-east-1.on.aws/?id=" . $allApp->app_packageName;
                    $res = Http::get($app_details_link);
                    if ($res->status() == 200) {
                        $repo_response = $res->getBody()->getContents();
                        $value = json_decode($repo_response);

                        // **** update app_details **** //
                        $get_app_details = AppDetails::where('app_packageName', $allApp->app_packageName)->first();
                        if ($get_app_details) {
                            // \Log::info($get_app_details->id);
                            $appDetails = AppDetails::find($get_app_details->id);
                            $appDetails->app_packageName = $allApp->app_packageName;
                            $appDetails->description = $value->description;
                            $appDetails->descriptionHTML = $value->descriptionHTML;
                            $appDetails->summary = $value->summary;
                            $appDetails->installs = $value->installs;
                            $appDetails->minInstalls = $value->minInstalls;
                            $appDetails->realInstalls = $value->realInstalls;
                            $appDetails->score = $value->score;
                            $appDetails->ratings = $value->ratings;
                            $appDetails->reviews = $value->reviews;
                            $appDetails->histogram = $value->histogram;
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
                            $appDetails->screenshots = $value->screenshots;
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
                            $appDetails->comments = $value->comments;
                            $appDetails->url = $value->url;
                            $appDetails->status = 'live';
                            $appDetails->save();
                        } else {
                            // \Log::info($allApp->app_packageName.' package add');
                            $appDetails = new AppDetails();
                            $appDetails->app_packageName = $allApp->app_packageName;
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
                        $response = $redis->get($allApp->app_packageName);
                        $app_res_redis = json_decode($response);

                        $allApp = AllApps::find($allApp->id);
                        $allApp->app_logo = $value->icon;
                        $allApp->app_name = $value->title;
                        $allApp->developer = $value->developer;
                        $allApp->app_privacyPolicyLink = $value->privacyPolicy;
                        if($app_res_redis->STATUS == 'true'){
                            $allApp->app_accountName = $app_res_redis->APP_SETTINGS->app_accountName;
                        }
                        $allApp->save();
                        // **** //

                    }
                }
            }

        }

        \Log::info("App Details update cron stop!");

    }
}
