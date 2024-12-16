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

        // Check if the SQL file exists
        if (!File::exists($path)) {
            $this->command->info("SQL file not found at: $path. Skipping this seeder.");
            return;
        }

        // Read the SQL file
        $sql = File::get($path);

        // Split the SQL content into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        // Execute each statement
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    DB::unprepared($statement . ';'); // Ensure a semicolon is added back
                } catch (\Exception $e) {
                    $this->command->error("Error executing statement: $statement");
                    $this->command->error($e->getMessage());
                }
            }
        }
    }
}
