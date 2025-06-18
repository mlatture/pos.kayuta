<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearReadings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'readings:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all data from the readings table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->confirm('Are you sure you want to delete ALL readings? This cannot be undone.')) {
            DB::table('readings')->delete();
            $this->info('All readings have been deleted.');
        } else {
            $this->info('Operation cancelled.');
        }

        return 0;

    }
}
