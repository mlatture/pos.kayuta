<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => 2,
                'name' => 'admin',
                'action' => 'Added new Rate',
                'created_at' => '2024-08-20 10:22:32',
                'updated_at' => '2024-08-20 10:22:32',
                'user_id' => 4,
            ],
            [
                'id' => 3,
                'name' => 'admin',
                'action' => 'Added new Rate',
                'created_at' => '2024-08-20 10:22:47',
                'updated_at' => '2024-08-20 10:22:47',
                'user_id' => 5,
            ],
        ];

        DB::table('activity_log')->insert($data);
    }
}
