<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CampsitePicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/campsite_pics.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            // Check if the SQL contains a valid INSERT statement
            if (preg_match('/INSERT INTO `campsite_pics` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                // Check if there are actual values in the insert statement
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Campsite Pics data seeded from campsite_pics.sql');
                } else {
                    $this->command->info('No data to insert into campsite_pics table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in campsite_pics.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }
    }
}
