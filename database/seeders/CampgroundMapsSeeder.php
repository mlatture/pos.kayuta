<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
        $data = [
            [
                'id' => 1,
                'image' => '2023-12-12-657880c71505f.jpg',
                'pdf' => '2023-09-27-651497c6c2a53.docx',
                'status' => 1,
                'created_at' => '2023-09-27 15:59:51',
                'updated_at' => '2023-12-12 10:48:23',
            ],
        ];

        DB::table('campground_maps')->insert($data);
    }
}
