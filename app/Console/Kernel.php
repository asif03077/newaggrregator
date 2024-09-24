<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    // Register the Artisan commands
    protected $commands = [
        \App\Console\Commands\ScrapeNewsAPIData::class,
         \App\Console\Commands\ScrapeNewYorkTimesData::class,
         \App\Console\Commands\ScrapeGuardianData::class,
    ];

    // Define the command schedule
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('scrape:newsapi')->hourly();
        $schedule->command('scrape:newyorkapi')->hourly();
        $schedule->command('scrape:guardianapi')->hourly();
        
    }

    // Register additional commands or events here if necessary
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
