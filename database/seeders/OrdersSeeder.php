<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/orders.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `orders` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Orders data seeded from orders.sql');
                } else {
                    $this->command->info('No data to insert into orders table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in orders.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }
    }
}
