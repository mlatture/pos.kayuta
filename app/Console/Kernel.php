<?php

namespace App\Console;

use App\Console\Commands\GenerateSqlDump;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\NewReservationController;

use App\Console\Commands\SendPaymentReminders;
use App\Console\Commands\OptimizedElectricPrompts;
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

        $schedule->job(new \App\Jobs\RefreshSocialTokensJob())->hourly();

        $schedule->command(SendPaymentReminders::class)->daily();
        $schedule->command('electric:optimize-prompts --min=8')->dailyAt('03:30')->onOneServer();
        $schedule
            ->call(function () {
                (new App\Http\Controllers\NewReservationController())->deleteCart();
            })
            ->everyMinutes();

        $schedule->call(function () {
            $year = Carbon::now()->year; 
            Artisan::call('seasonal:copy-to-next-year', [
                'year' => $year,
            ]);
        })->yearlyOn(11, 1, '02:00'); // e.g. every Nov 1st, 2AM

       // Weekly – e.g. every Monday at 02:00
        $schedule->command('ideas:generate')->weeklyOn(1, '2:00');

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
