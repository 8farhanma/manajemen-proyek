<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Improved reminder sending configuration
        $schedule->command('reminders:send')
            ->everyMinute()
            ->withoutOverlapping(10)  // 10 minute timeout
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/reminders.log'))
            ->emailOutputTo('admin@yourproject.com'); // Optional: send email if there are issues
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
