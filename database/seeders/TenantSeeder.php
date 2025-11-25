<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantSeeder extends Seeder
{
    public function run()
    {
        DB::table('tenants')->insert([
            'name'       => 'Kayuta Park',
            'slug'       => 'kayuta-park',
            'domain'     => 'admin.kayuta.com',
            'active'     => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
