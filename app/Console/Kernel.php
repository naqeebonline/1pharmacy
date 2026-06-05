<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */

    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
        \App\Console\Commands\GenerateDatabaseMarkdown::class,
    ];


    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
       // $schedule->command('sync:patient-data')->everyMinute();
        $schedule->command('sync:tolive')->everyMinute();
        $schedule->command('schema:markdown --path=docs/schema.md')->daily();
        
        // Automatic Database Backups - Daily at 6 AM, 2 PM, and 9 PM
        $schedule->command('backup:run --only-db')->dailyAt('11:18')->name('backup-6am');
        $schedule->command('backup:run --only-db')->dailyAt('02:00')->name('backup-2pm');
        $schedule->command('backup:run --only-db')->dailyAt('21:00')->name('backup-9pm');
        
        // Clean old backups daily at midnight
        $schedule->command('backup:clean')->daily()->at('00:00');
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
