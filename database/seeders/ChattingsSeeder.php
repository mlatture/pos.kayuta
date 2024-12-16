<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ChattingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $path = database_path('seeders/sql/chattings.sql');

        // // Check if the SQL file exists
        // if (!File::exists($path)) {
        //     $this->command->info("SQL file not found at: $path. Skipping this seeder.");
        //     return;
        // }

        // // Read the SQL file
        // $sql = File::get($path);

        // // Remove comments and blank lines
        // $sql = preg_replace('/--.*(\n|\r\n?)/', '', $sql); // Remove single-line comments
        // $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);   // Remove multi-line comments
        // $sql = preg_replace('/^\s*$/m', '', $sql);         // Remove blank lines

        // // Split the SQL into individual statements
        // $statements = array_filter(array_map('trim', explode(';', $sql)));

        // foreach ($statements as $statement) {
        //     if (!empty($statement)) {
        //         try {
        //             DB::unprepared($statement . ';'); // Add semicolon back if missing
        //         } catch (\Exception $e) {
        //             $this->command->error("Error executing statement: $statement");
        //             $this->command->error($e->getMessage());
        //         }
        //     }
        // }
    }
}
