<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $path = database_path('seeders/sql/customers.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `customers` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Customers data seeded from customers.sql');
                } else {
                    $this->command->info('No data to insert into customers table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in customers.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }

//        DB::table('customers')->insert([
//            [
//                'id' => 1,
//                'organization_id' => null,
//                'first_name' => 'Mark',
//                'last_name' => 'Latture',
//                'email' => 'mark@latture.com',
//                'phone' => '6148328377',
//                'address' => '47 Wilson Ave',
//                'avatar' => null,
//                'user_id' => 0,
//                'created_at' => '2024-09-06 10:58:59',
//                'updated_at' => '2024-09-06 10:58:59',
//            ],
//        ]);
    }
}
