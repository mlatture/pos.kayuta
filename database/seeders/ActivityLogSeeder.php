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

        if (!File::exists($path)) {
            $this->command->info("SQL file not found at: $path. Skipping this seeder.");
            return;
        }

        $sql = File::get($path);
        $insertStatements = '';
        preg_match_all('/INSERT INTO .+?;/is', $sql, $matches);

        if (!empty($matches[0])) {
            foreach ($matches[0] as $insertStatement) {
                if (preg_match('/INSERT INTO `(.+?)`.+VALUES \(.+?, \'(\d+)\',.+?\);/is', $insertStatement, $insertMatches)) {
                    $tableName = $insertMatches[1];
                    $recordId = $insertMatches[2];

                   
                    DB::unprepared("DELETE FROM `$tableName` WHERE id = '$recordId';");
                }
                
              
                $insertStatements .= $insertStatement . "\n";
            }
        }

        if (!empty($insertStatements)) {
            DB::unprepared($insertStatements);
        }
    }
}
