<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NewsLettersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('news_letters')->insert([
            [
                'id' => 1,
                'email' => 'mark@latture.com',
                'status' => 1,
                'created_at' => Carbon::create('2023', '10', '26', '00', '26', '11'),
                'updated_at' => Carbon::create('2023', '10', '26', '00', '26', '11'),
            ],
            [
                'id' => 2,
                'email' => 'm@latture.com',
                'status' => 1,
                'created_at' => Carbon::create('2023', '12', '02', '07', '27', '37'),
                'updated_at' => Carbon::create('2023', '12', '02', '07', '27', '37'),
            ],
        ]);
    }
}
