<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SitesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = File::get(database_path('seeders/sql/sites.sql'));

        $queries = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($queries as $query) {
            if (!empty($query)) {
                DB::statement($query);
            }
        }
    }
}
