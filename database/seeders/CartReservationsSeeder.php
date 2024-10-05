<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CartReservationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/cart_reservations.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `cart_reservations` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Cart Reservations data seeded from cart_reservations.sql');
                } else {
                    $this->command->info('No data to insert into cart_reservations table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in cart_reservations.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }
    }
}
