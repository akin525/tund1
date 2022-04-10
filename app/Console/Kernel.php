<?php

namespace App\Console;

use App\Console\Commands\CurrencyRate;
use App\Console\Commands\DatabaseBackUp;
use App\Console\Commands\GenerateVTPlans;
use App\Console\Commands\PromocodeGeneration;
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
        GenerateVTPlans::class,
        DatabaseBackUp::class,
        PromocodeGeneration::class,
        CurrencyRate::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        $schedule->command('backup:mysql --command=create')
            ->withoutOverlapping()
            ->dailyAt('23:59');

        $schedule->command('backup:mysql --command=delete')
            ->withoutOverlapping()
            ->weekly();

//        $schedule->command('backup:mysql --command=local')
//            ->withoutOverlapping()
//            ->hourly();

//        $schedule->command('promocode:generate')
//            ->withoutOverlapping()
//            ->dailyAt('04:00');

        $schedule->command('samji:fetchrates')
            ->withoutOverlapping()
            ->dailyAt('05:30');

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
}
