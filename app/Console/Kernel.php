<?php

namespace App\Console;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */

    protected $commands = [
//        Commands\SpyCron::class,
        Commands\AppDetailsUpadateCron::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        if (! $this->migrationRunning()) {

//            $schedule->command('spy:cron')->everyMinute();
//            $schedule->command('spy:cron')->cron('*/'.$setting->time.' * * * *');
//        }


        $setting = Setting::first();
        $user = User::where('roles','admin')->first();
        $schedule->command('AppDetailsUpdate:cron')->cron('*/'.$setting->time.' * * * *')->user($user);


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    public function migrationRunning()
    {
        if(app()->runningInConsole()) {
            $argv = \Request::server('argv', null);

            if ($argv) {
                return $argv[0] == 'artisan' && \Illuminate\Support\Str::contains($argv[1], 'migrate');
            }
        }

        return false;
    }
}
