<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GiftCardsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('gift_cards')->insert([
            [
                'id' => 1,
                'organization_id' => null,
                'title' => 'Card1',
                'user_email' => 'mlatture@gmail.com',
                'barcode' => '0927484940',
                'discount_type' => 'percentage',
                'discount' => 12,
                'start_date' => '2024-02-17',
                'expire_date' => '2025-02-28',
                'min_purchase' => 200,
                'max_discount' => 300,
                'limit' => 1,
                'status' => 1,
                'created_at' => '2024-02-17 18:29:56',
                'updated_at' => '2024-03-15 10:16:09',
                'amount' => 0,
                'modified_by' => null,
            ],
            [
                'id' => 2,
                'organization_id' => null,
                'title' => '10 percent off',
                'user_email' => null,
                'barcode' => '10%',
                'discount_type' => 'percentage',
                'discount' => 10,
                'start_date' => '2024-03-15',
                'expire_date' => '2024-03-29',
                'min_purchase' => 50,
                'max_discount' => 20,
                'limit' => 10,
                'status' => 1,
                'created_at' => '2024-03-15 13:33:26',
                'updated_at' => '2024-03-15 13:33:26',
                'amount' => 0,
                'modified_by' => null,
            ],
            [
                'id' => 3,
                'organization_id' => null,
                'title' => 'QAA',
                'user_email' => 'testcustomer@yopmail.com',
                'barcode' => '100000',
                'discount_type' => 'percentage',
                'discount' => 50,
                'start_date' => '2024-05-04',
                'expire_date' => '2024-05-13',
                'min_purchase' => 10,
                'max_discount' => 100,
                'limit' => 1,
                'status' => 1,
                'created_at' => '2024-05-04 12:17:42',
                'updated_at' => '2024-05-04 12:21:05',
                'amount' => 0,
                'modified_by' => null,
            ],
            [
                'id' => 4,
                'organization_id' => null,
                'title' => '5 Dollars off',
                'user_email' => 'mark@latture.com',
                'barcode' => '$5.00',
                'discount_type' => 'fixed_amount',
                'discount' => 5,
                'start_date' => '2024-05-10',
                'expire_date' => '2034-05-10',
                'min_purchase' => 100,
                'max_discount' => 5,
                'limit' => 1,
                'status' => 1,
                'created_at' => '2024-05-10 08:15:18',
                'updated_at' => '2024-05-10 08:15:18',
                'amount' => 0,
                'modified_by' => null,
            ],
        ]);
    }
}
