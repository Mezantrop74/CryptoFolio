<?php

namespace App\Console;

use App\Console\Commands\ParseTelegramChannels;
use App\Console\Commands\RefreshCrypto;
use App\Console\Commands\RefreshRate;
use App\Console\Commands\RefreshWatchOnlyWallets;
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
        RefreshCrypto::class,
        RefreshRate::class,
        RefreshWatchOnlyWallets::class,
        ParseTelegramChannels::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('crypto:refresh')->twiceDaily();
        $schedule->command('rate:refresh')->everyMinute();
        $schedule->command('wallets:refresh')->everyTenMinutes();
        $schedule->command('telegram:parse')->everyTenMinutes();
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
