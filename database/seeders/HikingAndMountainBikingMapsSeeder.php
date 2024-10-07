<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class HikingAndMountainBikingMapsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/hiking_and_mountain_biking_maps.sql');

        if (!File::exists($path)) {
            $this->command->info("SQL file not found at: $path. Skipping this seeder.");
            return;
        }

        $sql = File::get($path);
        DB::unprepared($sql);
    }
}
