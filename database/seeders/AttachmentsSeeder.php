<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class AttachmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Path to the SQL file
        $path = database_path('seeders/sql/attachments.sql');

        // Check if the file exists
        if (!File::exists($path)) {
            $this->command->info("SQL file not found at: $path. Skipping this seeder.");
            return;
        }

        // Read the contents of the SQL file
        $sql = File::get($path);

        // Split the SQL content into separate statements using ";" as a delimiter
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        // Execute each statement individually
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    DB::unprepared($statement . ';'); // Add ";" back to each statement
                } catch (\Exception $e) {
                    $this->command->error("Error executing statement: $statement");
                    $this->command->error($e->getMessage());
                }
            }
        }
    }
}
