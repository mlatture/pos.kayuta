<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CacheLocksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/cache_locks.sql');

        if (!File::exists($path)) {
            $this->command->info("SQL file not found at: $path. Skipping this seeder.");
            return;
        }

        $sql = File::get($path);
        $insertStatements = '';
        preg_match_all('/INSERT INTO .+?;/is', $sql, $matches);

        if (!empty($matches[0])) {
            $insertStatements = implode("\n", $matches[0]);
        }
        DB::unprepared($insertStatements);
    }
}
