<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdminRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $path = database_path('seeders/sql/admin_roles.sql');
////
////        if (File::exists($path)) {
//            $sql = File::get($path);
//            DB::insert($sql);
//dd($sql);
//            if (preg_match('/INSERT INTO `admin_roles` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
//                DB::unprepared($sql);
//                if (trim($matches[1]) !== '') {
//                    DB::unprepared($sql);
//                    $this->command->info('Admin Roles data seeded from admin_roles.sql');
//                } else {
//                    $this->command->info('No data to insert into admin_roles table. Skipping...');
//                }
//            } else {
//                $this->command->info('No valid INSERT statement found in admin_roles.sql. Skipping...');
//            }
//        } else {
//            $this->command->error('SQL file not found at ' . $path);
//        }
        $path = database_path('seeders/sql/admin_roles.sql');
        $sql = File::get($path);

        // Use a regular expression to extract only INSERT statements
        $insertStatements = '';
        preg_match_all('/INSERT INTO .+?;/is', $sql, $matches);

        if (!empty($matches[0])) {
            $insertStatements = implode("\n", $matches[0]);
        }

        // Execute the INSERT statements if found
        if (!empty($insertStatements)) {
            DB::unprepared($insertStatements); // Use unprepared since it might be multiple insert statements
//            dd($insertStatements); // For debugging, it will show the SQL queries being executed
        }

//        $data = [
//            [
//                'id' => 1,
//                'name' => 'Master Admin',
//                'module_access' => null,
//                'status' => 1,
//                'created_at' => null,
//                'updated_at' => null,
//            ],
//            [
//                'id' => 7,
//                'name' => 'Manager',
//                'module_access' => json_encode([
//                    "dashboard", "rate_tier_management", "camping_season_management",
//                    "sites_management", "reservation_management", "events_management",
//                    "season_management", "blog_management", "content_management",
//                    "promotion_management", "user_section", "system_settings"
//                ]),
//                'status' => 1,
//                'created_at' => '2023-05-05 15:47:48',
//                'updated_at' => '2023-12-13 08:41:51',
//            ],
//            [
//                'id' => 8,
//                'name' => 'SuperAdmin',
//                'module_access' => json_encode([
//                    "dashboard", "rate_tier_management", "camping_season_management",
//                    "sites_management", "reservation_management", "events_management",
//                    "season_management", "blog_management", "content_management",
//                    "promotion_management", "user_section", "system_settings"
//                ]),
//                'status' => 1,
//                'created_at' => '2023-12-02 07:34:47',
//                'updated_at' => '2023-12-13 08:41:34',
//            ],
//            [
//                'id' => 9,
//                'name' => 'SiteEditor',
//                'module_access' => json_encode(["dashboard", "sites_management"]),
//                'status' => 1,
//                'created_at' => '2023-12-02 08:18:20',
//                'updated_at' => '2023-12-13 14:47:43',
//            ],
//        ];
//
//        DB::table('admin_roles')->insert($data);
    }
}
