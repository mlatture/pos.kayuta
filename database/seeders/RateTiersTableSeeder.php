<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RateTiersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rate_tiers')->insert([
            ['tier' => 'WE30A', 'minimumstay' => 1, 'useflatrate' => 1, 'flatrate' => 57, 'usedynamic' => 1, 'dynamicincrease' => 10, 'dynamicincreasepercent' => 0, 'dynamicdecrease' => 2, 'dynamicdecreasepercent' => 0, 'lastminuteincrease' => 0, 'lastminutedays' => 0, 'earlybookingincrease' => 0, 'earlybookingdays' => 0, 'weeklyrate' => 300, 'monthlyrate' => 500, 'seasonalrate' => 0, 'sundayrate' => 57, 'mondayrate' => 57, 'tuesdayrate' => 57, 'wednesdayrate' => 55, 'thursdayrate' => 57, 'fridayrate' => 63, 'saturdayrate' => 63, 'orderby' => 0, 'lastmodified' => '2023-12-19 10:49:32'],
            ['tier' => 'WSE30A', 'minimumstay' => 1, 'useflatrate' => 1, 'flatrate' => 62, 'usedynamic' => 1, 'dynamicincrease' => 10, 'dynamicincreasepercent' => 0, 'dynamicdecrease' => 2, 'dynamicdecreasepercent' => 0, 'lastminuteincrease' => 0, 'lastminutedays' => 0, 'earlybookingincrease' => 0, 'earlybookingdays' => 0, 'weeklyrate' => 300, 'monthlyrate' => 500, 'seasonalrate' => 0, 'sundayrate' => 62, 'mondayrate' => 62, 'tuesdayrate' => 62, 'wednesdayrate' => 58, 'thursdayrate' => 59, 'fridayrate' => 65, 'saturdayrate' => 65, 'orderby' => 0, 'lastmodified' => '2023-12-19 10:49:42'],
            ['tier' => 'WE50A', 'minimumstay' => 1, 'useflatrate' => 1, 'flatrate' => 62, 'usedynamic' => 1, 'dynamicincrease' => 10, 'dynamicincreasepercent' => 0, 'dynamicdecrease' => 2, 'dynamicdecreasepercent' => 0, 'lastminuteincrease' => 0, 'lastminutedays' => 0, 'earlybookingincrease' => 0, 'earlybookingdays' => 0, 'weeklyrate' => 0, 'monthlyrate' => 0, 'seasonalrate' => 0, 'sundayrate' => 62, 'mondayrate' => 62, 'tuesdayrate' => 62, 'wednesdayrate' => 58, 'thursdayrate' => 59, 'fridayrate' => 65, 'saturdayrate' => 65, 'orderby' => 0, 'lastmodified' => '2023-03-18 04:06:15'],
            ['tier' => 'WSE50A', 'minimumstay' => 1, 'useflatrate' => 1, 'flatrate' => 67, 'usedynamic' => 1, 'dynamicincrease' => 10, 'dynamicincreasepercent' => 0, 'dynamicdecrease' => 2, 'dynamicdecreasepercent' => 0, 'lastminuteincrease' => 0, 'lastminutedays' => 0, 'earlybookingincrease' => 0, 'earlybookingdays' => 0, 'weeklyrate' => 0, 'monthlyrate' => 0, 'seasonalrate' => 0, 'sundayrate' => 62, 'mondayrate' => 62, 'tuesdayrate' => 62, 'wednesdayrate' => 58, 'thursdayrate' => 59, 'fridayrate' => 65, 'saturdayrate' => 65, 'orderby' => 0, 'lastmodified' => '2023-03-18 04:09:32'],
            ['tier' => 'CABIN', 'minimumstay' => 2, 'useflatrate' => 1, 'flatrate' => 150, 'usedynamic' => 0, 'dynamicincrease' => 10, 'dynamicincreasepercent' => 0, 'dynamicdecrease' => 8, 'dynamicdecreasepercent' => 0.1, 'lastminuteincrease' => 10, 'lastminutedays' => 30, 'earlybookingincrease' => 15, 'earlybookingdays' => 100, 'weeklyrate' => 0, 'monthlyrate' => 500, 'seasonalrate' => 0, 'sundayrate' => 150, 'mondayrate' => 150, 'tuesdayrate' => 150, 'wednesdayrate' => 150, 'thursdayrate' => 150, 'fridayrate' => 150, 'saturdayrate' => 150, 'orderby' => 0, 'lastmodified' => '2023-12-19 10:49:20'],
            ['tier' => 'BOAT', 'minimumstay' => 1, 'useflatrate' => 1, 'flatrate' => 15, 'usedynamic' => 1, 'dynamicincrease' => 10, 'dynamicincreasepercent' => 0, 'dynamicdecrease' => 2, 'dynamicdecreasepercent' => 0, 'lastminuteincrease' => 0, 'lastminutedays' => 0, 'earlybookingincrease' => 0, 'earlybookingdays' => 0, 'weeklyrate' => 0, 'monthlyrate' => 0, 'seasonalrate' => 0, 'sundayrate' => 10, 'mondayrate' => 20, 'tuesdayrate' => 20, 'wednesdayrate' => 15, 'thursdayrate' => 20, 'fridayrate' => 30, 'saturdayrate' => 30, 'orderby' => 0, 'lastmodified' => '2023-03-12 07:39:12'],
            ['tier' => 'JETSKI', 'minimumstay' => 1, 'useflatrate' => 1, 'flatrate' => 15, 'usedynamic' => 1, 'dynamicincrease' => 10, 'dynamicincreasepercent' => 0, 'dynamicdecrease' => 2, 'dynamicdecreasepercent' => 0, 'lastminuteincrease' => 0, 'lastminutedays' => 0, 'earlybookingincrease' => 0, 'earlybookingdays' => 0, 'weeklyrate' => 0, 'monthlyrate' => 0, 'seasonalrate' => 0, 'sundayrate' => 10, 'mondayrate' => 20, 'tuesdayrate' => 20, 'wednesdayrate' => 15, 'thursdayrate' => 20, 'fridayrate' => 30, 'saturdayrate' => 30, 'orderby' => 0, 'lastmodified' => '2023-03-18 04:08:31'],
            ['tier' => 'RETRO', 'minimumstay' => 1, 'useflatrate' => 1, 'flatrate' => 100, 'usedynamic' => 1, 'dynamicincrease' => 10, 'dynamicincreasepercent' => 0, 'dynamicdecrease' => 2, 'dynamicdecreasepercent' => 0, 'lastminuteincrease' => 0, 'lastminutedays' => 0, 'earlybookingincrease' => 0, 'earlybookingdays' => 0, 'weeklyrate' => 500, 'monthlyrate' => 1500, 'seasonalrate' => 0, 'sundayrate' => 105, 'mondayrate' => 105, 'tuesdayrate' => 105, 'wednesdayrate' => 105, 'thursdayrate' => 105, 'fridayrate' => 115, 'saturdayrate' => 115, 'orderby' => 0, 'lastmodified' => '2023-12-19 10:48:28'],
        ]);
    }
}
