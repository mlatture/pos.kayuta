<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MenuItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menu_items')->insert([
            [
                'id' => 1,
                'title' => 'netflix',
                'url' => 'https://www.netflix.com/browse',
                'target' => 'window',
                'order' => 1,
                'created_at' => Carbon::create('2024', '08', '06', '01', '27', '38'),
                'updated_at' => Carbon::create('2024', '08', '06', '03', '33', '40'),
            ],
            [
                'id' => 2,
                'title' => 'Google',
                'url' => 'https://www.google.com/',
                'target' => 'window',
                'order' => 2,
                'created_at' => Carbon::create('2024', '08', '06', '01', '29', '30'),
                'updated_at' => Carbon::create('2024', '08', '06', '03', '34', '45'),
            ],
            [
                'id' => 3,
                'title' => 'awd',
                'url' => 'https://www.w3schools.com/jsref/met_win_open.asp',
                'target' => '_self',
                'order' => 3,
                'created_at' => Carbon::create('2024', '08', '06', '03', '21', '18'),
                'updated_at' => Carbon::create('2024', '08', '06', '03', '47', '04'),
            ],
        ]);
    }
}
