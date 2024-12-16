<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CacheSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $path = database_path('seeders/sql/cache.sql');

        // // Check if the SQL file exists
        // if (!File::exists($path)) {
        //     $this->command->info("SQL file not found at: $path. Skipping this seeder.");
        //     return;
        // }

        // // Read the SQL file
        // $sql = File::get($path);

        // // Remove comments and blank lines
        // $sql = preg_replace('/--.*(\n|\r\n?)/', '', $sql); // Strip single-line comments
        // $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);   // Strip multi-line comments
        // $sql = preg_replace('/^\s*$/m', '', $sql);         // Remove blank lines

        // // Split the SQL content into individual statements
        // $statements = array_filter(array_map('trim', explode(';', $sql)));

        // // Execute each statement
        // foreach ($statements as $statement) {
        //     if (!empty($statement)) {
        //         try {
        //             DB::unprepared($statement . ';'); // Ensure semicolon at the end
        //         } catch (\Exception $e) {
        //             $this->command->error("Error executing statement: $statement");
        //             $this->command->error($e->getMessage());
        //         }
        //     }
        // }
    }
}
