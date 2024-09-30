<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteClassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('site_classes')->insert([
            [
                'id' => 1,
                'siteclass' => 'RV Sites',
                'showriglength' => 1,
                'showhookup' => 1,
                'showrigtype' => 0,
                'tax' => '',
                'orderby' => 1,
                'created_at' => null,
                'updated_at' => null
            ],
            [
                'id' => 2,
                'siteclass' => 'Boat Slips',
                'showriglength' => 0,
                'showhookup' => 0,
                'showrigtype' => 0,
                'tax' => '',
                'orderby' => 6,
                'created_at' => null,
                'updated_at' => null
            ],
            [
                'id' => 9,
                'siteclass' => 'Cabin',
                'showriglength' => 0,
                'showhookup' => 0,
                'showrigtype' => 0,
                'tax' => 'Cabin_Tax',
                'orderby' => 4,
                'created_at' => null,
                'updated_at' => null
            ],
            [
                'id' => 11,
                'siteclass' => 'Tent Sites',
                'showriglength' => 0,
                'showhookup' => 0,
                'showrigtype' => 0,
                'tax' => '',
                'orderby' => 2,
                'created_at' => null,
                'updated_at' => null
            ],
            [
                'id' => 25,
                'siteclass' => 'Jet Ski Slips',
                'showriglength' => 0,
                'showhookup' => 0,
                'showrigtype' => 0,
                'tax' => '',
                'orderby' => 20,
                'created_at' => null,
                'updated_at' => null
            ]
        ]);
    }
}
