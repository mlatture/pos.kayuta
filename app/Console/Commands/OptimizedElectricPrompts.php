<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use App\Services\ElectricPromptOptimizer;

class OptimizedElectricPrompts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'electric:optimize-prompts {--min=8 : Minimum failed examples per group} {--dry-run : Do not write prompts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily job: optimize meter_page prompts using grouped failed electric readings';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ElectricPromptOptimizer $optimizer)
    {
        $min = (int)$this->option('min');
        $dryRun = (bool)$this->option('dry-run');

        $this->info("Running electric prompt optimizer (min group size: {$min}, dry-run:".($dryRun ? 'yes' : 'no' ) . ")");
        
        try {
            $summary = $optimizer->run($min, $dryRun);
            $this->info("Optimization completed. Summary:");
            foreach ($summary as $line) {
                $this->line($line);
            }
            return 0;
        } catch (\Exception $e) {
            Log::error('OptimizedElectricPrompts failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $this->error("Error during optimization: " . $e->getMessage());
            return 1;
        }


    }
}
