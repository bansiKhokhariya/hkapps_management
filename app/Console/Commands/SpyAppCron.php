<?php

namespace App\Console\Commands;

use App\Models\SpyApps;
use Illuminate\Console\Command;
use Nelexa\GPlay\Model\GoogleImage;

class SpyAppCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SpyApp:cron';

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

        \Log::info('all new spy app save!');

    }
}
