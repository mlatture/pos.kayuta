<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User;
use App\Models\Site;
use App\Models\SeasonalSetting;
use App\Models\SeasonalRenewal;

use Illuminate\Support\Str;

class GenerateSeasonalRenewals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seasonal:generate-renewals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate seasonal renewal records for users marked as seasonal';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $setting = SeasonalSetting::latest()->first();

        if (!$setting) {
            $this->error('No seasonal settings found.');
            return 1;
        }

        $users = User::where('seasonal', true)->with('site')->get();
        $count = 0;

        foreach ($users as $user) {
            $tier = $user->site->ratetier ?? null;
            $rate = $setting->rate_tiers[$tier] ?? $setting->default_rate;

            $renewal = SeasonalRenewal::updateOrCreate(
                ['customer_id' => $user->id],
                [
                    'offered_rate' => $rate,
                    'status' => 'pending',
                    'token' => Str::random(64),
                    'renewed' => false,
                    'response_date' => null,
                    'notes' => null,
                ],
            );

            $count++;
        }

        $this->info("Generated or updated $count seasonal renewal records.");
        return 0;
    }
}
