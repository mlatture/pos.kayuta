<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CouponsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/coupons.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `coupons` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Coupons data seeded from coupons.sql');
                } else {
                    $this->command->info('No data to insert into coupons table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in coupons.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }
    }
}
