<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class KayutaVideosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/kayuta_videos.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `kayuta_videos` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Kayuta Videos data seeded from kayuta_videos.sql');
                } else {
                    $this->command->info('No data to insert into kayuta_videos table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in kayuta_videos.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }

//        DB::table('kayuta_videos')->insert([
//            'id' => 1,
//            'video' => '2023-11-05-654773ef2f09b.mp4',
//            'status' => 1,
//            'created_at' => '2023-09-27 15:19:43',
//            'updated_at' => '2023-11-05 10:52:31',
//        ]);
    }
}
