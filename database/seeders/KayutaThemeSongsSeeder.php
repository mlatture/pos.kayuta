<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
        DB::table('kayuta_theme_songs')->insert([
            'id' => 1,
            'title' => 'Kayuta Theme Song',
            'description' => '<h1>Kayuta Lake Campground<br />Theme song</h1><h3>Performed by&nbsp;<a href="https://www.fiverr.com/franktavis" target="_blank">Frank Tavis</a></h3>',
            'video_link' => null,
            'status' => 1,
            'created_at' => '2023-09-28 17:28:03',
            'updated_at' => '2023-11-05 14:31:28',
        ]);
    }
}
