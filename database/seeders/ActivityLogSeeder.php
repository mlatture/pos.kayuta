<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/activity_log.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            $sql = preg_replace('/LOCK TABLES.*?;/is', '', $sql);
            $sql = preg_replace('/UNLOCK TABLES;/', '', $sql);

            if (preg_match_all('/INSERT INTO `activity_log` .*?VALUES\s*(.*?);/is', $sql, $matches)) {
                $insertStatements = implode('', $matches[0]);

                DB::unprepared($insertStatements);

                $this->command->info('Activity log data seeded from activity_log.sql');
            } else {
                $this->command->info('No valid INSERT statements found in activity_log.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }
    }

//        $data = [
//            [
//                'id' => 2,
//                'name' => 'admin',
//                'action' => 'Added new Rate',
//                'created_at' => '2024-08-20 10:22:32',
//                'updated_at' => '2024-08-20 10:22:32',
//                'user_id' => 4,
//            ],
//            [
//                'id' => 3,
//                'name' => 'admin',
//                'action' => 'Added new Rate',
//                'created_at' => '2024-08-20 10:22:47',
//                'updated_at' => '2024-08-20 10:22:47',
//                'user_id' => 5,
//            ],
//        ];
//
//        DB::table('activity_log')->insert($data);
//    }
}
