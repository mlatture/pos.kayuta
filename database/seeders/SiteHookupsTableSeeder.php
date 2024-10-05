<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class SiteHookupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/site_hookups.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `site_hookups` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Site Hookups data seeded from site_hookups.sql');
                } else {
                    $this->command->info('No data to insert into site_hookups table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in site_hookups.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }

//        DB::table('site_hookups')->insert([
//            [
//                'id' => 1,
//                'sitehookup' => 'WE30A',
//                'orderby' => 1,
//                'created_at' => null,
//                'updated_at' => null
//            ],
//            [
//                'id' => 2,
//                'sitehookup' => 'WSE30A',
//                'orderby' => 2,
//                'created_at' => null,
//                'updated_at' => null
//            ],
//            [
//                'id' => 3,
//                'sitehookup' => 'WSE50A',
//                'orderby' => 3,
//                'created_at' => null,
//                'updated_at' => null
//            ],
//            [
//                'id' => 4,
//                'sitehookup' => 'WE50A',
//                'orderby' => 4,
//                'created_at' => null,
//                'updated_at' => null
//            ],
//            [
//                'id' => 5,
//                'sitehookup' => 'No Hookup',
//                'orderby' => 100,
//                'created_at' => null,
//                'updated_at' => null
//            ]
//        ]);
    }
}
