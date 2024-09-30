<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            ['id' => 1, 'organization_id' => null, 'name' => 'Food', 'status' => 1, 'created_at' => '2024-02-16 11:38:20', 'updated_at' => '2024-02-16 11:38:25'],
            ['id' => 2, 'organization_id' => null, 'name' => 'Drinks', 'status' => 1, 'created_at' => '2024-02-18 00:50:24', 'updated_at' => '2024-02-18 00:50:24'],
            ['id' => 3, 'organization_id' => null, 'name' => 'Apparel', 'status' => 1, 'created_at' => '2024-03-15 11:45:11', 'updated_at' => '2024-03-15 11:49:06'],
            ['id' => 4, 'organization_id' => null, 'name' => 'Grocery', 'status' => 1, 'created_at' => '2024-03-15 11:48:10', 'updated_at' => '2024-03-15 11:48:10'],
            ['id' => 5, 'organization_id' => null, 'name' => 'Rentals/Activities', 'status' => 1, 'created_at' => '2024-03-15 11:48:18', 'updated_at' => '2024-03-15 11:50:38'],
            ['id' => 6, 'organization_id' => null, 'name' => 'Candy', 'status' => 1, 'created_at' => '2024-03-15 11:48:39', 'updated_at' => '2024-03-15 11:48:39'],
            ['id' => 7, 'organization_id' => null, 'name' => 'Fishing', 'status' => 1, 'created_at' => '2024-03-15 11:48:51', 'updated_at' => '2024-03-15 11:48:51'],
            ['id' => 8, 'organization_id' => null, 'name' => 'Supplies', 'status' => 1, 'created_at' => '2024-03-15 11:49:45', 'updated_at' => '2024-03-15 11:49:45'],
            ['id' => 9, 'organization_id' => null, 'name' => 'Toys', 'status' => 1, 'created_at' => '2024-03-15 11:49:55', 'updated_at' => '2024-03-15 11:49:55'],
            ['id' => 10, 'organization_id' => null, 'name' => 'Apothecary', 'status' => 0, 'created_at' => '2024-03-15 11:51:00', 'updated_at' => '2024-03-15 11:51:00'],
            ['id' => 12, 'organization_id' => null, 'name' => 'Test', 'status' => 1, 'created_at' => '2024-05-04 09:02:06', 'updated_at' => '2024-05-04 09:02:06'],
            ['id' => 13, 'organization_id' => null, 'name' => 'Stuffed "Yettis"', 'status' => 1, 'created_at' => '2024-05-09 20:27:44', 'updated_at' => '2024-05-09 20:27:44'],
        ]);
    }
}
