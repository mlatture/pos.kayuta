<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/currencies.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `currencies` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Currencies data seeded from currencies.sql');
                } else {
                    $this->command->info('No data to insert into currencies table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in currencies.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }
//        DB::table('currencies')->insert([
//            ['id' => 1, 'name' => 'USD', 'symbol' => '$', 'code' => 'USD', 'exchange_rate' => '1', 'status' => 1, 'created_at' => '2021-06-27 13:39:37', 'updated_at' => '2021-06-27 13:39:37'],
//            ['id' => 2, 'name' => 'BDT', 'symbol' => '৳', 'code' => 'BDT', 'exchange_rate' => '84', 'status' => 1, 'created_at' => '2021-07-06 11:52:58', 'updated_at' => '2021-07-06 11:52:58'],
//            ['id' => 3, 'name' => 'Indian Rupee', 'symbol' => '₹', 'code' => 'INR', 'exchange_rate' => '60', 'status' => 1, 'created_at' => '2020-10-15 17:23:04', 'updated_at' => '2021-06-04 18:26:38'],
//            ['id' => 4, 'name' => 'Euro', 'symbol' => '€', 'code' => 'EUR', 'exchange_rate' => '100', 'status' => 1, 'created_at' => '2021-05-25 21:00:23', 'updated_at' => '2021-06-04 18:25:29'],
//            ['id' => 5, 'name' => 'YEN', 'symbol' => '¥', 'code' => 'JPY', 'exchange_rate' => '110', 'status' => 1, 'created_at' => '2021-06-10 22:08:31', 'updated_at' => '2021-06-26 14:21:10'],
//            ['id' => 6, 'name' => 'Ringgit', 'symbol' => 'RM', 'code' => 'MYR', 'exchange_rate' => '4.16', 'status' => 1, 'created_at' => '2021-07-03 11:08:33', 'updated_at' => '2021-07-03 11:10:37'],
//            ['id' => 7, 'name' => 'Rand', 'symbol' => 'R', 'code' => 'ZAR', 'exchange_rate' => '14.26', 'status' => 1, 'created_at' => '2021-07-03 11:12:38', 'updated_at' => '2021-07-03 11:12:42'],
//        ]);
    }
}
