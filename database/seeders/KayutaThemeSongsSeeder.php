<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class KayutaThemeSongsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/kayuta_theme_songs.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `kayuta_theme_songs` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Kayuta Theme Songs data seeded from kayuta_theme_songs.sql');
                } else {
                    $this->command->info('No data to insert into kayuta_theme_songs table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in kayuta_theme_songs.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }

//        DB::table('kayuta_theme_songs')->insert([
//            'id' => 1,
//            'title' => 'Kayuta Theme Song',
//            'description' => '<h1>Kayuta Lake Campground<br />Theme song</h1><h3>Performed by&nbsp;<a href="https://www.fiverr.com/franktavis" target="_blank">Frank Tavis</a></h3>',
//            'video_link' => null,
//            'status' => 1,
//            'created_at' => '2023-09-28 17:28:03',
//            'updated_at' => '2023-11-05 14:31:28',
//        ]);
    }
}
