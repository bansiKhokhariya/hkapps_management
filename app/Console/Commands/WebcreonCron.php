<?php

namespace App\Console\Commands;

use App\Models\App;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class WebcreonCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'WebCreon:cron';

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
        \Log::info("start webcreon cron!");
//        $redis = Redis::connection('RedisApp6');
//        $response = $redis->keys("*");
//        if (count($response) > 0) {
//            foreach ($response as $packageName) {
//                $app_details_link = "https://play.google.com/store/apps/details?id=" . $packageName;
//
//                $res = Http::get($app_details_link);
//                if ($res->status() == 200) {
//                    $getWebCreon = App::where('package_name', $packageName)->first();
//                    if (!$getWebCreon) {
//                        \Log::info('package add ' . $packageName);
//                        $appDetails_link = "https://lplciltdwh6kd6qjl4ytd6tzoq0iaumr.lambda-url.us-east-1.on.aws/?id=" . $packageName;
//                        $appDetails_res = Http::get($appDetails_link);
//                        $app_response = json_decode($appDetails_res->getBody()->getContents());
//
//
//                        $redis = Redis::connection('RedisApp2');
//                        $response = $redis->get($packageName);
//                        $app_res_redis = json_decode($response);
//
//                        if ($app_res_redis->STATUS == 'true') {
//
//                            $app = new App();
//                            $app->title = $app_response->title;
//                            $app->package_name = $packageName;
//                            $app->icon = $app_response->icon;
//                            $app->developer = $app_response->developer;
//                            $app->app_accountName = $app_res_redis->APP_SETTINGS->app_accountName;
//                            $app->status = 'live';
//                            $app->save();
//
////                            $app_delete_link = "https://webcreon.com/update/deleteindb6?pkg=" . $packageName;
////                            $app_delete_link_res = Http::get($app_delete_link);
//
//                        }
//                    }
//                }
//            }
//        }
        \Log::info('webcron run stop succesfully');
    }
}
