<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
        DB::table('site_rigtypes')->insert([
            [
                'id' => 1,
                'rigtype' => 'Class B Van',
                'orderby' => 5,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 2,
                'rigtype' => '5th Wheel',
                'orderby' => 2,
                'created_at' => null,
                'updated_at' => null,
            ]
        ]);
    }
}
