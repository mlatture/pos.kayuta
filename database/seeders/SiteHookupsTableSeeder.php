<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
        DB::table('site_hookups')->insert([
            [
                'id' => 1,
                'sitehookup' => 'WE30A',
                'orderby' => 1,
                'created_at' => null,
                'updated_at' => null
            ],
            [
                'id' => 2,
                'sitehookup' => 'WSE30A',
                'orderby' => 2,
                'created_at' => null,
                'updated_at' => null
            ],
            [
                'id' => 3,
                'sitehookup' => 'WSE50A',
                'orderby' => 3,
                'created_at' => null,
                'updated_at' => null
            ],
            [
                'id' => 4,
                'sitehookup' => 'WE50A',
                'orderby' => 4,
                'created_at' => null,
                'updated_at' => null
            ],
            [
                'id' => 5,
                'sitehookup' => 'No Hookup',
                'orderby' => 100,
                'created_at' => null,
                'updated_at' => null
            ]
        ]);
    }
}
