<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class SiteRigtypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/site_rigtypes.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `site_rigtypes` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Site Rigtypes data seeded from site_rigtypes.sql');
                } else {
                    $this->command->info('No data to insert into site_rigtypes table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in site_rigtypes.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }

//        DB::table('site_rigtypes')->insert([
//            [
//                'id' => 1,
//                'rigtype' => 'Class B Van',
//                'orderby' => 5,
//                'created_at' => null,
//                'updated_at' => null,
//            ],
//            [
//                'id' => 2,
//                'rigtype' => '5th Wheel',
//                'orderby' => 2,
//                'created_at' => null,
//                'updated_at' => null,
//            ]
//        ]);
    }
}
