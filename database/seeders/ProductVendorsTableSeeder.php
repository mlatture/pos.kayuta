<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductVendorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_vendors')->insert([
            [
                'id' => 1,
                'organization_id' => null,
                'name' => 'A&P Master Images',
                'address_1' => 'water st',
                'address_2' => null,
                'city' => 'utica',
                'state' => 'ny',
                'zip' => '13338',
                'country' => 'USA',
                'contact_name' => 'Howard',
                'email' => 'hpotter@masteryourimage.com',
                'work_phone' => null,
                'mobile_phone' => null,
                'fax' => null,
                'notes' => null,
                'created_at' => Carbon::createFromFormat('Y-m-d H:i:s', '2024-05-17 15:37:48'),
                'updated_at' => Carbon::createFromFormat('Y-m-d H:i:s', '2024-05-17 15:37:48'),
            ],
        ]);
    }
}
