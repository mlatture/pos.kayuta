<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class NewsLettersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/news_letters.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `news_letters` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('News Letters data seeded from news_letters.sql');
                } else {
                    $this->command->info('No data to insert into news_letters table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in news_letters.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }

//        DB::table('news_letters')->insert([
//            [
//                'id' => 1,
//                'email' => 'mark@latture.com',
//                'status' => 1,
//                'created_at' => Carbon::create('2023', '10', '26', '00', '26', '11'),
//                'updated_at' => Carbon::create('2023', '10', '26', '00', '26', '11'),
//            ],
//            [
//                'id' => 2,
//                'email' => 'm@latture.com',
//                'status' => 1,
//                'created_at' => Carbon::create('2023', '12', '02', '07', '27', '37'),
//                'updated_at' => Carbon::create('2023', '12', '02', '07', '27', '37'),
//            ],
//        ]);
    }
}
