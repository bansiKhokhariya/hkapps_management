<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Models\Setting;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        Commands\AppDetailsUpadateCron::class,
        Commands\CheckAppStatusCron::class,
        Commands\WebcreonCron::class,
        Commands\SpyAppCron::class,
        Commands\SpyAppDetailsCron::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // // app details update cron //
        // $appDetailsUpdate = Setting::where('cron','AppDetailsUpdate')->first();
        // $schedule->command('AppDetailsUpdate:cron')->cron('*/'.$appDetailsUpdate->time.' * * * *');
        // // check app status cron //
        // $checkAppStatus = Setting::where('cron','CheckAppStatus')->first();
        // $schedule->command('CheckAppStatus:cron')->cron('*/'.$checkAppStatus->time.' * * * *');
        // // WebCreon cron //
        // $webCreon = Setting::where('cron','WebCreon')->first();
        // $schedule->command('WebCreon:cron')->cron('*/'.$webCreon->time.' * * * *');
        // // SpyApp cron //
        // $spyApp = Setting::where('cron','SpyApp')->first();
        // $schedule->command('SpyApp:cron')->cron('*/'.$spyApp->time.' * * * *');
        // // SpyAppDetails cron //
        // $spyAppDetails = Setting::where('cron','SpyAppDetails')->first();
        // $schedule->command('SpyAppDetails:cron')->cron('*/'.$spyAppDetails->time.' * * * *');


        //  app details update cron //
        $appDetailsUpdate = Setting::where('cron', 'AppDetailsUpdate')->first();
        switch ($appDetailsUpdate->time) {
            case('everyMinute'):
                $schedule->command('AppDetailsUpdate:cron')->everyMinute()->withoutOverlapping();
                break;
            case('everyTwoMinutes'):
                $schedule->command('AppDetailsUpdate:cron')->everyTwoMinutes()->withoutOverlapping();
                break;
            case('everyThreeMinutes'):
                $schedule->command('AppDetailsUpdate:cron')->everyThreeMinutes()->withoutOverlapping();
                break;
            case('everyFourMinutes'):
                $schedule->command('AppDetailsUpdate:cron')->everyFourMinutes()->withoutOverlapping();
                break;
            case('everyFiveMinutes'):
                $schedule->command('AppDetailsUpdate:cron')->everyFiveMinutes()->withoutOverlapping();
                break;
            case('everyTenMinutes'):
                $schedule->command('AppDetailsUpdate:cron')->everyTenMinutes()->withoutOverlapping();
                break;
            case('everyFifteenMinutes'):
                $schedule->command('AppDetailsUpdate:cron')->everyFifteenMinutes()->withoutOverlapping();
                break;
            case('everyThirtyMinutes'):
                $schedule->command('AppDetailsUpdate:cron')->everyThirtyMinutes()->withoutOverlapping();
                break;
            case('hourly'):
                $schedule->command('AppDetailsUpdate:cron')->hourly();
                break;
            case('everyTwoHours'):
                $schedule->command('AppDetailsUpdate:cron')->everyTwoHours();
                break;
            case('everyThreeHours'):
                $schedule->command('AppDetailsUpdate:cron')->everyThreeHours();
                break;
            case('everyFourHours'):
                $schedule->command('AppDetailsUpdate:cron')->everyFourHours();
                break;
            case('everySixHours'):
                $schedule->command('AppDetailsUpdate:cron')->everySixHours();
                break;
            case('daily'):
                $schedule->command('AppDetailsUpdate:cron')->dailyAt('09:00');
                break;
            case('weekly'):
                $schedule->command('AppDetailsUpdate:cron')->weekly();
                break;
            case('monthly'):
                $schedule->command('AppDetailsUpdate:cron')->monthly();
                break;
            case('quarterly'):
                $schedule->command('AppDetailsUpdate:cron')->quarterly();
                break;
            default:
                \Log::info("Something went wrong app details.");
        }


        // check app status cron //
        $checkAppStatus = Setting::where('cron', 'CheckAppStatus')->first();
        switch ($checkAppStatus->time) {
            case('everyMinute'):
                $schedule->command('CheckAppStatus:cron')->everyMinute()->withoutOverlapping();
                break;
            case('everyTwoMinutes'):
                $schedule->command('CheckAppStatus:cron')->everyTwoMinutes()->withoutOverlapping();
                break;
            case('everyThreeMinutes'):
                $schedule->command('CheckAppStatus:cron')->everyThreeMinutes()->withoutOverlapping();
                break;
            case('everyFourMinutes'):
                $schedule->command('CheckAppStatus:cron')->everyFourMinutes()->withoutOverlapping();
                break;
            case('everyFiveMinutes'):
                $schedule->command('CheckAppStatus:cron')->everyFiveMinutes()->withoutOverlapping();
                break;
            case('everyTenMinutes'):
                $schedule->command('CheckAppStatus:cron')->everyTenMinutes()->withoutOverlapping();
                break;
            case('everyFifteenMinutes'):
                $schedule->command('CheckAppStatus:cron')->everyFifteenMinutes()->withoutOverlapping();
                break;
            case('everyThirtyMinutes'):
                $schedule->command('CheckAppStatus:cron')->everyThirtyMinutes()->withoutOverlapping();
                break;
            case('hourly'):
                $schedule->command('CheckAppStatus:cron')->hourly();
                break;
            case('everyTwoHours'):
                $schedule->command('CheckAppStatus:cron')->everyTwoHours();
                break;
            case('everyThreeHours'):
                $schedule->command('CheckAppStatus:cron')->everyThreeHours();
                break;
            case('everyFourHours'):
                $schedule->command('CheckAppStatus:cron')->everyFourHours();
                break;
            case('everySixHours'):
                $schedule->command('CheckAppStatus:cron')->everySixHours();
                break;
            case('daily'):
                $schedule->command('CheckAppStatus:cron')->dailyAt('09:00');
                break;
            case('weekly'):
                $schedule->command('CheckAppStatus:cron')->weekly();
                break;
            case('monthly'):
                $schedule->command('CheckAppStatus:cron')->monthly();
                break;
            case('quarterly'):
                $schedule->command('CheckAppStatus:cron')->quarterly();
                break;
            default:
                \Log::info("Something went wrong check app status");
        }

        // SpyApp cron //
        $spyApp = Setting::where('cron', 'SpyApp')->first();
        switch ($spyApp->time) {
            case('everyMinute'):
                $schedule->command('SpyApp:cron')->everyMinute()->withoutOverlapping();
                break;
            case('everyTwoMinutes'):
                $schedule->command('SpyApp:cron')->everyTwoMinutes()->withoutOverlapping();
                break;
            case('everyThreeMinutes'):
                $schedule->command('SpyApp:cron')->everyThreeMinutes()->withoutOverlapping();
                break;
            case('everyFourMinutes'):
                $schedule->command('SpyApp:cron')->everyFourMinutes()->withoutOverlapping();
                break;
            case('everyFiveMinutes'):
                $schedule->command('SpyApp:cron')->everyFiveMinutes()->withoutOverlapping();
                break;
            case('everyTenMinutes'):
                $schedule->command('SpyApp:cron')->everyTenMinutes()->withoutOverlapping();
                break;
            case('everyFifteenMinutes'):
                $schedule->command('SpyApp:cron')->everyFifteenMinutes()->withoutOverlapping();
                break;
            case('everyThirtyMinutes'):
                $schedule->command('SpyApp:cron')->everyThirtyMinutes()->withoutOverlapping();
                break;
            case('hourly'):
                $schedule->command('SpyApp:cron')->hourly();
                break;
            case('everyTwoHours'):
                $schedule->command('SpyApp:cron')->everyTwoHours();
                break;
            case('everyThreeHours'):
                $schedule->command('SpyApp:cron')->everyThreeHours();
                break;
            case('everyFourHours'):
                $schedule->command('SpyApp:cron')->everyFourHours();
                break;
            case('everySixHours'):
                $schedule->command('SpyApp:cron')->everySixHours();
                break;
            case('daily'):
                $schedule->command('SpyApp:cron')->dailyAt('09:00');
                break;
            case('weekly'):
                $schedule->command('SpyApp:cron')->weekly();
                break;
            case('monthly'):
                $schedule->command('SpyApp:cron')->monthly();
                break;
            case('quarterly'):
                $schedule->command('SpyApp:cron')->quarterly();
                break;
            default:
                \Log::info("Something went wrong spy app.");
        }

        // SpyAppDetails cron //

        // $schedule->command('SpyAppDetails:cron')->daily();
        $spyAppDetails = Setting::where('cron', 'SpyAppDetails')->first();
        switch ($spyAppDetails->time) {
            case('everyMinute'):
                $schedule->command('SpyAppDetails:cron')->everyMinute()->withoutOverlapping();
                break;
            case('everyTwoMinutes'):
                $schedule->command('SpyAppDetails:cron')->everyTwoMinutes()->withoutOverlapping();
                break;
            case('everyThreeMinutes'):
                $schedule->command('SpyAppDetails:cron')->everyThreeMinutes()->withoutOverlapping();
                break;
            case('everyFourMinutes'):
                $schedule->command('SpyAppDetails:cron')->everyFourMinutes()->withoutOverlapping();
                break;
            case('everyFiveMinutes'):
                $schedule->command('SpyAppDetails:cron')->everyFiveMinutes()->withoutOverlapping();
                break;
            case('everyTenMinutes'):
                $schedule->command('SpyAppDetails:cron')->everyTenMinutes()->withoutOverlapping();
                break;
            case('everyFifteenMinutes'):
                $schedule->command('SpyAppDetails:cron')->everyFifteenMinutes()->withoutOverlapping();
                break;
            case('everyThirtyMinutes'):
                $schedule->command('SpyAppDetails:cron')->everyThirtyMinutes()->withoutOverlapping();
                break;
            case('hourly'):
                $schedule->command('SpyAppDetails:cron')->hourly();
                break;
            case('everyTwoHours'):
                $schedule->command('SpyAppDetails:cron')->everyTwoHours();
                break;
            case('everyThreeHours'):
                $schedule->command('SpyAppDetails:cron')->everyThreeHours();
                break;
            case('everyFourHours'):
                $schedule->command('SpyAppDetails:cron')->everyFourHours();
                break;
            case('everySixHours'):
                $schedule->command('SpyAppDetails:cron')->everySixHours();
                break;
            case('daily'):
                $schedule->command('SpyAppDetails:cron')->dailyAt('10:00');
                break;
            case('weekly'):
                $schedule->command('SpyAppDetails:cron')->weekly();
                break;
            case('monthly'):
                $schedule->command('SpyAppDetails:cron')->monthly();
                break;
            case('quarterly'):
                $schedule->command('SpyAppDetails:cron')->quarterly();
                break;
            default:
                \Log::info("Something went wrong spy app details.");
        }


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
