<?php

namespace App\Console\Commands;

use App\Models\AllApps;
use App\Models\AppDetails;
use App\Models\TestAllApp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

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
        $users = AllApps::get();

        if ($users->count() > 0) {
            foreach ($users as $allApp) {

                $app_details_link = "https://lplciltdwh6kd6qjl4ytd6tzoq0iaumr.lambda-url.us-east-1.on.aws/?id=" . $allApp->app_packageName;
                $res = Http::get($app_details_link);
                if ($res->status() == 200) {
                    $repo_response = $res->getBody()->getContents();
                    $value = json_decode($repo_response);

                    // **** update app_details **** //
                    $get_app_details = AppDetails::where('allApps_id', $allApp->id)->first();
                    if ($get_app_details) {

                        $appDetails = AppDetails::find($get_app_details->id);
                        $appDetails->allApps_id = $allApp->id;
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
                        $appDetails->save();
                    }else{
                        $appDetails = new AppDetails();
                        $appDetails->allApps_id = $allApp->id;
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
                        $appDetails->status = 'publish';
                        $appDetails->save();
                    }

                    // **** //

                    // **** update all_apps **** //

                    $allApp = AllApps::find($allApp->id);
                    $allApp->app_logo = $value->icon;
                    $allApp->app_name = $value->title;
                    $allApp->app_privacyPolicyLink = $value->privacyPolicy;
                    $allApp->save();

                    // **** //

                    // **** update test_all_apps **** //

                    $allApp = TestAllApp::find($allApp->id);
                    $allApp->app_logo = $value->icon;
                    $allApp->app_name = $value->title;
                    $allApp->app_privacyPolicyLink = $value->privacyPolicy;
                    $allApp->save();

                    // **** //
                }
            }
        }

        \Log::info("App Details updated succesfully!");

    }
}
