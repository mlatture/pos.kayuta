<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminRolesSeeder extends Seeder
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
                'id' => 1,
                'name' => 'Master Admin',
                'module_access' => null,
                'status' => 1,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 7,
                'name' => 'Manager',
                'module_access' => json_encode([
                    "dashboard", "rate_tier_management", "camping_season_management",
                    "sites_management", "reservation_management", "events_management",
                    "season_management", "blog_management", "content_management",
                    "promotion_management", "user_section", "system_settings"
                ]),
                'status' => 1,
                'created_at' => '2023-05-05 15:47:48',
                'updated_at' => '2023-12-13 08:41:51',
            ],
            [
                'id' => 8,
                'name' => 'SuperAdmin',
                'module_access' => json_encode([
                    "dashboard", "rate_tier_management", "camping_season_management",
                    "sites_management", "reservation_management", "events_management",
                    "season_management", "blog_management", "content_management",
                    "promotion_management", "user_section", "system_settings"
                ]),
                'status' => 1,
                'created_at' => '2023-12-02 07:34:47',
                'updated_at' => '2023-12-13 08:41:34',
            ],
            [
                'id' => 9,
                'name' => 'SiteEditor',
                'module_access' => json_encode(["dashboard", "sites_management"]),
                'status' => 1,
                'created_at' => '2023-12-02 08:18:20',
                'updated_at' => '2023-12-13 14:47:43',
            ],
        ];

        DB::table('admin_roles')->insert($data);
    }
}
