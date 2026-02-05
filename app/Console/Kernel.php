<?php
// app/Console/Kernel.php

namespace App\Console;

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
        \App\Console\Commands\SendDailyFestivalNotifications::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Run daily festival notifications at 6 PM
        $schedule->command('notifications:daily-festival')
            ->dailyAt('18:00')
            ->timezone('Asia/Kolkata') // Adjust to your timezone
            ->appendOutputTo(storage_path('logs/festival-notifications.log'));

        // Process Google Calendar reminders based on user days
        $schedule->command('calendar:process-reminders')
            ->everyTenMinutes()
            ->appendOutputTo(storage_path('logs/calendar-processor.log'));

        // Sync Indian Holidays from Google (Daily)
        $schedule->command('calendar:sync-holidays')
            ->daily()
            ->appendOutputTo(storage_path('logs/holiday-sync.log'));

        // Optional: Test the command every minute (for testing only)
        // $schedule->command('notifications:daily-festival')->everyMinute();
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