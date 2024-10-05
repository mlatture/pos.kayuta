<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class PosPaymentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $path = database_path('seeders/sql/pos_payments.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `pos_payments` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('POS Payments data seeded from pos_payments.sql');
                } else {
                    $this->command->info('No data to insert into pos_payments table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in pos_payments.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }
    }
}
