<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/products.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `products` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Products data seeded from products.sql');
                } else {
                    $this->command->info('No data to insert into products table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in products.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }

//        $data = [
//            [
//                'id' => 1,
//                'organization_id' => null,
//                'category_id' => 2,
//                'tax_type_id' => null,
//                'product_vendor_id' => null,
//                'name' => 'Akeem Velasquez',
//                'description' => 'Aut velit veritatis',
//                'image' => 'products/qTFpxpKRiXtPuQQF9nTJUsDgrS2xpg3PTe94d9Fw.jpg',
//                'barcode' => '0000',
//                'price' => 851.50,
//                'quantity' => -1,
//                'type' => null,
//                'discount_type' => 'fixed_amount',
//                'discount' => 5,
//                'status' => 1,
//                'created_at' => '2024-02-16 14:18:00',
//                'updated_at' => '2024-04-14 07:15:01',
//            ],
//            [
//                'id' => 2,
//                'organization_id' => null,
//                'category_id' => 1,
//                'tax_type_id' => 2,
//                'product_vendor_id' => null,
//                'name' => 'yetti',
//                'description' => null,
//                'image' => 'products/kznwC210sqDQbqlswwDNO0KRO137OksSrF9SmOwS.jpg',
//                'barcode' => '1',
//                'price' => 6.25,
//                'quantity' => 50,
//                'type' => null,
//                'discount_type' => null,
//                'discount' => 0,
//                'status' => 0,
//                'created_at' => '2024-02-18 01:06:33',
//                'updated_at' => '2024-05-21 05:56:21',
//            ],
//            [
//                'id' => 3,
//                'organization_id' => null,
//                'category_id' => 1,
//                'tax_type_id' => 2,
//                'product_vendor_id' => null,
//                'name' => 'LaserTag',
//                'description' => null,
//                'image' => '',
//                'barcode' => '0',
//                'price' => 10.00,
//                'quantity' => 0,
//                'type' => null,
//                'discount_type' => null,
//                'discount' => 0,
//                'status' => 1,
//                'created_at' => '2024-03-15 10:24:21',
//                'updated_at' => '2024-03-15 10:25:26',
//            ],
//            [
//                'id' => 4,
//                'organization_id' => null,
//                'category_id' => 5,
//                'tax_type_id' => 1,
//                'product_vendor_id' => null,
//                'name' => 'Laser Tag',
//                'description' => 'pew pew',
//                'image' => 'products/UzuPkVKWprDjdmj4aY2BSF0pzipFz9gQ11sJaWgV.png',
//                'barcode' => '14564',
//                'price' => 10.00,
//                'quantity' => 0,
//                'type' => null,
//                'discount_type' => '',
//                'discount' => 0,
//                'status' => 1,
//                'created_at' => '2024-03-15 12:03:29',
//                'updated_at' => '2024-04-27 00:35:26',
//            ],
//            [
//                'id' => 5,
//                'organization_id' => null,
//                'category_id' => 2,
//                'tax_type_id' => 1,
//                'product_vendor_id' => null,
//                'name' => 'bobobobobobobobobobobobobobobobobobobobobobobobobobobobob',
//                'description' => 'bobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobobo',
//                'image' => '',
//                'barcode' => '545415kjk',
//                'price' => 100.00,
//                'quantity' => 1,
//                'type' => null,
//                'discount_type' => '',
//                'discount' => 0,
//                'status' => 1,
//                'created_at' => '2024-03-15 12:13:02',
//                'updated_at' => '2024-03-15 12:13:02',
//            ],
//            [
//                'id' => 6,
//                'organization_id' => null,
//                'category_id' => 4,
//                'tax_type_id' => 1,
//                'product_vendor_id' => null,
//                'name' => 'B Complex Vitamins',
//                'description' => null,
//                'image' => 'products/ajqRvUw44x3U4FEkJ6oos9Ho5PHuwEuaVYQeRLXj.png',
//                'barcode' => '031604027278',
//                'price' => 10.59,
//                'quantity' => 98,
//                'type' => null,
//                'discount_type' => null,
//                'discount' => 0,
//                'status' => 1,
//                'created_at' => '2024-03-15 12:24:38',
//                'updated_at' => '2024-05-04 08:58:03',
//            ],
//            [
//                'id' => 8,
//                'organization_id' => null,
//                'category_id' => 9,
//                'tax_type_id' => 6,
//                'product_vendor_id' => null,
//                'name' => 'Yetti',
//                'description' => 'Pick up a new Yetti today.',
//                'image' => 'products/0vvlBbETSg2DuIdd2gQ0eZKWM0KyYrouIMJxxYEN.jpg',
//                'barcode' => 'm',
//                'price' => 18.99,
//                'quantity' => 345,
//                'type' => null,
//                'discount_type' => null,
//                'discount' => 0,
//                'status' => 1,
//                'created_at' => '2024-04-14 09:52:19',
//                'updated_at' => '2024-05-21 05:47:01',
//            ],
//            [
//                'id' => 9,
//                'organization_id' => null,
//                'category_id' => 12,
//                'tax_type_id' => 7,
//                'product_vendor_id' => null,
//                'name' => 'Test Product 10',
//                'description' => 'This is a product added for testing purpose....',
//                'image' => 'products/Tt3TrS7Q5L7o3LB77i5xDbd1ihSTKaUIVVqyK8mF.webp',
//                'barcode' => '100000',
//                'price' => 100.00,
//                'quantity' => 6,
//                'type' => null,
//                'discount_type' => 'percentage',
//                'discount' => 10,
//                'status' => 1,
//                'created_at' => '2024-05-04 09:04:19',
//                'updated_at' => '2024-05-04 12:22:16',
//            ],
//            [
//                'id' => 10,
//                'organization_id' => null,
//                'category_id' => 4,
//                'tax_type_id' => 9,
//                'product_vendor_id' => 1,
//                'name' => 'Cheese Curds',
//                'description' => 'curds of cheese',
//                'image' => '',
//                'barcode' => null,
//                'price' => 8.99,
//                'quantity' => 4,
//                'type' => null,
//                'discount_type' => '',
//                'discount' => 0,
//                'status' => 1,
//                'created_at' => '2024-05-17 15:38:56',
//                'updated_at' => '2024-05-17 15:38:56',
//            ],
//            [
//                'id' => 11,
//                'organization_id' => null,
//                'category_id' => 1,
//                'tax_type_id' => 6,
//                'product_vendor_id' => 1,
//                'name' => "Lion's Mane Mus",
//                'description' => '4200mg supplements',
//                'image' => '',
//                'barcode' => null,
//                'price' => 10.59,
//                'quantity' => 99,
//                'type' => null,
//                'discount_type' => '',
//                'discount' => 0,
//                'status' => 1,
//                'created_at' => '2024-06-02 01:02:34',
//                'updated_at' => '2024-06-02 01:02:34',
//            ],
//            [
//                'id' => 12,
//                'organization_id' => null,
//                'category_id' => 1,
//                'tax_type_id' => null,
//                'product_vendor_id' => null,
//                'name' => 'Lemon Essential Oil',
//                'description' => null,
//                'image' => 'products/qTf7NGrVAv3rVb6EZ13Qxx7E9acGkK2SgYuE1fD6.jpg',
//                'barcode' => null,
//                'price' => 6.49,
//                'quantity' => 99,
//                'type' => null,
//                'discount_type' => '',
//                'discount' => 0,
//                'status' => 1,
//                'created_at' => '2024-06-02 01:02:34',
//                'updated_at' => '2024-06-02 01:02:34',
//            ],
//        ];
//
//        DB::table('products')->insert($data);
    }
}
