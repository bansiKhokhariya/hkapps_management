<?php

namespace App\Console\Commands;

use App\Models\AllApps;
use App\Models\AppDetails;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckAppStatusCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckAppStatus:cron';

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
        \Log::info("check App status cron!");
        $allApps = AllApps::get();

        if ($allApps->count() > 0) {
            foreach ($allApps as $allApp) {

                $app_details_link = "https://play.google.com/store/apps/details?id=" . $allApp->app_packageName;
                $res = Http::get($app_details_link);
                if ($res->status() == 200) {

                }else{
                    $get_app = AllApps::where('app_packageName', $allApp->app_packageName)->first();
                    if ($get_app) {
                        \Log::info($get_app->id);
                        // send app remove notification //
//                        $user = User::where('roles', 'admin')->first();
//                        $auth_user = User::find($user->id);
//                        $notification = $user;
//                        $app_details = [
//                            'Package Name' => $get_app->app_packageName,
//                            'App Name' => $get_app->app_name,
//                            'Icon' => $get_app->app_logo,
//                        ];
//                        $notification->notify(new RemoveAppNotification($app_details,$auth_user));
                        //  ****** //

                        $get_app->status = 'removed';
                        $get_app->save();

                    } else {
                        \Log::info('app not found');
                    }
                }
            }
        }

        \Log::info("check App status cron run succesfully!");
    }
}
