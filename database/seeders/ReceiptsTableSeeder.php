<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReceiptsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('receipts')->insert([
            ['id' => 1, 'cartid' => '653d76770fe6c', 'createdate' => '2023-10-28 17:01:35', 'lastmodified' => '2023-10-28 17:01:35', 'updated_at' => '2023-10-29 01:01:35', 'created_at' => '2023-10-29 01:01:35'],
            ['id' => 2, 'cartid' => '6543b8d455b4d', 'createdate' => '2023-11-02 15:12:39', 'lastmodified' => '2023-11-02 15:12:39', 'updated_at' => '2023-11-02 15:12:39', 'created_at' => '2023-11-02 15:12:39'],
            ['id' => 3, 'cartid' => '6543bcc3add44', 'createdate' => '2023-11-02 15:25:50', 'lastmodified' => '2023-11-02 15:25:50', 'updated_at' => '2023-11-02 15:25:50', 'created_at' => '2023-11-02 15:25:50'],
            ['id' => 4, 'cartid' => '6543beffe8ff3', 'createdate' => '2023-11-02 15:25:50', 'lastmodified' => '2023-11-02 15:25:50', 'updated_at' => '2023-11-02 15:25:50', 'created_at' => '2023-11-02 15:25:50'],
            ['id' => 5, 'cartid' => '654661a893401', 'createdate' => '2023-11-04 15:27:57', 'lastmodified' => '2023-11-04 15:27:57', 'updated_at' => '2023-11-04 15:27:57', 'created_at' => '2023-11-04 15:27:57'],
            ['id' => 6, 'cartid' => '6546623743a41', 'createdate' => '2023-11-04 15:27:57', 'lastmodified' => '2023-11-04 15:27:57', 'updated_at' => '2023-11-04 15:27:57', 'created_at' => '2023-11-04 15:27:57'],
            ['id' => 7, 'cartid' => '6546680679eb4', 'createdate' => '2023-11-05 13:14:17', 'lastmodified' => '2023-11-05 13:14:17', 'updated_at' => '2023-11-05 13:14:17', 'created_at' => '2023-11-05 13:14:17'],
            ['id' => 8, 'cartid' => '65478dc25bd8a', 'createdate' => '2023-11-05 13:14:17', 'lastmodified' => '2023-11-05 13:14:17', 'updated_at' => '2023-11-05 13:14:17', 'created_at' => '2023-11-05 13:14:17'],
            ['id' => 9, 'cartid' => '654de35ef20d1', 'createdate' => '2023-11-10 08:54:55', 'lastmodified' => '2023-11-10 08:54:55', 'updated_at' => '2023-11-10 08:54:55', 'created_at' => '2023-11-10 08:54:55'],
            ['id' => 10, 'cartid' => '654de3982f907', 'createdate' => '2023-11-10 08:54:55', 'lastmodified' => '2023-11-10 08:54:55', 'updated_at' => '2023-11-10 08:54:55', 'created_at' => '2023-11-10 08:54:55'],
            ['id' => 11, 'cartid' => '654deff73f7a9', 'createdate' => '2023-11-10 08:55:42', 'lastmodified' => '2023-11-10 08:55:42', 'updated_at' => '2023-11-10 08:55:42', 'created_at' => '2023-11-10 08:55:42'],
            ['id' => 12, 'cartid' => '656b4f40a8384', 'createdate' => '2023-12-02 15:38:24', 'lastmodified' => '2023-12-02 15:38:24', 'updated_at' => '2023-12-02 10:38:24', 'created_at' => '2023-12-02 10:38:24'],
            ['id' => 13, 'cartid' => '6579e1e05dbf2', 'createdate' => '2023-12-13 16:58:05', 'lastmodified' => '2023-12-13 16:58:05', 'updated_at' => '2023-12-13 11:58:05', 'created_at' => '2023-12-13 11:58:05'],
            ['id' => 14, 'cartid' => '657b842786cab', 'createdate' => '2023-12-14 22:40:43', 'lastmodified' => '2023-12-14 22:40:43', 'updated_at' => '2023-12-14 17:40:43', 'created_at' => '2023-12-14 17:40:43'],
            ['id' => 15, 'cartid' => '657b84477c350', 'createdate' => '2023-12-14 22:40:43', 'lastmodified' => '2023-12-14 22:40:43', 'updated_at' => '2023-12-14 17:40:43', 'created_at' => '2023-12-14 17:40:43'],
        ]);
    }
}
