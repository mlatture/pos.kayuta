<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ReadingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/readings.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `readings` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Readings data seeded from readings.sql');
                } else {
                    $this->command->info('No data to insert into readings table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in readings.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }

//        DB::table('readings')->insert([
//            ['id' => 1, 'customer_id' => 25, 'kwhNo' => '1000', 'image' => null, 'date' => '2024-08-16', 'siteno' => '300', 'status' => 'Please pay the bills online or visit the camp store.', 'bill' => '120.00', 'created_at' => now(), 'updated_at' => now()],
//            ['id' => 2, 'customer_id' => 25, 'kwhNo' => '1000', 'image' => null, 'date' => '2024-08-16', 'siteno' => '300', 'status' => 'Paid Bills', 'bill' => '120.00', 'created_at' => now(), 'updated_at' => now()],
//            ['id' => 3, 'customer_id' => 25, 'kwhNo' => '100', 'image' => '1723887636.png', 'date' => '2024-08-17', 'siteno' => 'CR02', 'status' => 'Please pay the bills online or visit the camp store.', 'bill' => '12.00', 'created_at' => now(), 'updated_at' => now()],
//            // Add more rows here as needed...
//            ['id' => 4, 'customer_id' => 25, 'kwhNo' => '100', 'image' => '1723887637.png', 'date' => '2024-08-17', 'siteno' => 'CR02', 'status' => 'Please pay the bills online or visit the camp store.', 'bill' => '12.00', 'created_at' => now(), 'updated_at' => now()],
//            ['id' => 5, 'customer_id' => 25, 'kwhNo' => '100', 'image' => '1723887649.png', 'date' => '2024-08-17', 'siteno' => 'CR02', 'status' => 'Please pay the bills online or visit the camp store.', 'bill' => '12.00', 'created_at' => now(), 'updated_at' => now()],
//            // Continue adding all other records similarly...
//        ]);
    }
}
