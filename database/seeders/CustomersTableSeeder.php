<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
        DB::table('customers')->insert([
            [
                'id' => 1,
                'organization_id' => null,
                'first_name' => 'Mark',
                'last_name' => 'Latture',
                'email' => 'mark@latture.com',
                'phone' => '6148328377',
                'address' => '47 Wilson Ave',
                'avatar' => null,
                'user_id' => 0,
                'created_at' => '2024-09-06 10:58:59',
                'updated_at' => '2024-09-06 10:58:59',
            ],
        ]);
    }
}
