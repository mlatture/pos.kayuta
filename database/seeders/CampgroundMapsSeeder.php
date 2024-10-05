<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CampgroundMapsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/campground_maps.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `campground_maps` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Campground Maps data seeded from campground_maps.sql');
                } else {
                    $this->command->info('No data to insert into campground_maps table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in campground_maps.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }

//        $data = [
//            [
//                'id' => 1,
//                'image' => '2023-12-12-657880c71505f.jpg',
//                'pdf' => '2023-09-27-651497c6c2a53.docx',
//                'status' => 1,
//                'created_at' => '2023-09-27 15:59:51',
//                'updated_at' => '2023-12-12 10:48:23',
//            ],
//        ];
//
//        DB::table('campground_maps')->insert($data);
    }
}
