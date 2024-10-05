<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class SeasonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/seasons.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `seasons` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Seasons data seeded from seasons.sql');
                } else {
                    $this->command->info('No data to insert into seasons table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in seasons.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }

//        DB::table('seasons')->insert([
//            'id' => 1,
//            'name' => 'Seasonal Site Info',
//            'description' => '<h3>Why Vacation Just Once A Summer, When You Can Vacation Every Weekend, All Summer Long At Kayuta Lake Campground?</h3>
//            <p>Seasonal Rates include water & sewer or weekly pump out, picnic table & fire ring. Electric is metered and billed monthly. Convenient interest-free winter payment plans to meet your family&#39;s budget!</p>
//            <p>Create lasting friendships and family memories when you rent a seasonal site at Kayuta Lake Campground!</p>
//            <p>Please note, that the camping unit should be an RV (travel trailer, pop up, Motorhome or 5th wheel) and 10 years old or newer.</p>
//            <p>Anything older than 10 years will need to be approved by management. We do not accept certain kinds of Park Models. Please see management regarding details.</p>
//            <p>We require that you camp here a few times to see what we&#39;re all about before we will place you on the list. It helps you get to know us and we get to know you. We will not place anyone on a seasonal site who has never stayed at the park.</p>',
//            'image' => '2023-10-28-653d5cf9e9467.webp',
//            'sub_heading' => 'The 2024 Seasonal Rate Is $3,200 Seasonal Site Waiting List',
//            'year' => '2024',
//            'created_at' => '2023-10-28 14:02:48',
//            'updated_at' => '2023-10-28 14:11:53',
//        ]);
    }
}
