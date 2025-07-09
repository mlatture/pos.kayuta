<?php

namespace App\Console;

use App\Console\Commands\GenerateSqlDump;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\NewReservationController;

use App\Console\Commands\SendPaymentReminders;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [GenerateSqlDump::class, \App\Console\Commands\GenerateSeasonalRenewals::class];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        $schedule->command(SendPaymentReminders::class)->daily();
        $schedule
            ->call(function () {
                (new App\Http\Controllers\NewReservationController())->deleteCart();
            })
            ->everyMinutes();

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
