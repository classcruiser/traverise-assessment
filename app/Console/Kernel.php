<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use App\Jobs\GenerateAutomatedEmails;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\DatabaseBackUp'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        try {
            $schedule->job(new GenerateAutomatedEmails())->everyMinute();
            $schedule->command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();
            $schedule->command('queue:restart')->hourly();
            $schedule->command('delete_unpaid_multi-pass_orders:cron')->everyTwoHours();
            $schedule->command('database:backup')->daily();
            $schedule->call(function () {
                Log::info('Schedule ran successfully on '. date('Y-m-d H:i:s'));
            })->daily();
        } catch (\Exception $e) {
            Log::info('Cannot run schedule '. $e->getMessage());
        }
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
