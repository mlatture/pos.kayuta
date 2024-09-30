<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
        DB::table('kayuta_videos')->insert([
            'id' => 1,
            'video' => '2023-11-05-654773ef2f09b.mp4',
            'status' => 1,
            'created_at' => '2023-09-27 15:19:43',
            'updated_at' => '2023-11-05 10:52:31',
        ]);
    }
}
