<?php

namespace App\Console\Commands;

use App\Models\SpyAppDetails;
use App\Models\SpyApps;
use Illuminate\Console\Command;

class SpyAppDetailsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SpyAppDetails:cron';

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
        $getSpyApp = SpyApps::get();

        if ($getSpyApp->count() > 0) {
            foreach ($getSpyApp as $spyApp) {

                $gPlay = new \Nelexa\GPlay\GPlayApps();

                $checkApp = $gPlay->existsApp($spyApp->packageName);

                if ($checkApp > 0) {

                    $appInfo = $gPlay->getAppInfo($spyApp->packageName);

                    $spyAppDetails = SpyAppDetails::where('packageName', $spyApp->packageName)->latest()->first();
                    if ($spyAppDetails) {
                        $dailyInstalls = $appInfo->installs - $spyAppDetails->downloads;
                    } else {
                        $dailyInstalls = 0;
                    }
                    $spyAppDetails = new SpyAppDetails();
                    $spyAppDetails->packageName = $spyApp->packageName;
                    $spyAppDetails->downloads = $appInfo->installs;
                    $spyAppDetails->ratings = $appInfo->score;
                    $spyAppDetails->reviews = $appInfo->numberReviews;
                    $spyAppDetails->daily_installs = $dailyInstalls;
                    $spyAppDetails->save();
                }

            }

            \Log::info('all spy app details save!');

        }
    }
}
