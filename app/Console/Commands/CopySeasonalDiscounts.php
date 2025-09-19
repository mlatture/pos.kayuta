<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SeasonalCustomerDiscount;
use App\Models\SystemLog;

class CopySeasonalDiscounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seasonal:copy-to-next-year {year}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy active seasonal discounts from one year to the next year';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $year = (int) $this->argument('year');
        $nextYear = $year + 1;

        $discounts = SeasonalCustomerDiscount::where('season_year', $year)->where('is_active', true)->get();

        $this->info("Found {$discounts->count()} active discounts in {$year}.");

        $copied = 0;

        foreach ($discounts as $discount) {
            $exists = SeasonalCustomerDiscount::where('customer_id', $discount->customer_id)->where('season_year', $nextYear)->where('discount_type', $discount->discount_type)->where('discount_value', $discount->discount_value)->exists();

            if ($exists) {
                $this->warn("Skipped (already exists): Customer {$discount->customer_id}");
                continue;
            }

            SeasonalCustomerDiscount::create([
                'customer_id' => $discount->customer_id,
                'discount_type' => $discount->discount_type,
                'discount_value' => $discount->discount_value,
                'description' => $discount->description,
                'is_active' => true,
                'season_year' => $nextYear,
            ]);

            SystemLog::create([
                'action' => 'discount_copied',
                'user_id' => auth()->id() ?? null, // if run manually, might be null
                'details' => "Discount for customer {$discount->customer_id} copied from {$year} to {$nextYear}",
                'created_at' => Carbon::now(),
            ]);

            $copied++;
        }

        $this->info("✅ Done. Copied {$copied} discounts into {$nextYear}.");
    }
}
