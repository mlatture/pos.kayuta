<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\ScheduledPayment;
use App\Models\SeasonalRenewal;
use Illuminate\Support\Facades\DB;

class ClearSeasonalData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seasonal:clear-data {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all data from scheduled_payments and seasonal_renewals';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('⚠️ This will delete ALL records in scheduled_payments and seasonal_renewals. Are you sure?')) {
                $this->warn('Aborted.');
                return Command::SUCCESS;
            }
        }

        DB::transaction(function () {
            ScheduledPayment::truncate();
            SeasonalRenewal::truncate();
        });

        $this->info('✅ scheduled_payments and seasonal_renewals tables have been cleared.');
        return Command::SUCCESS;
    }
}
