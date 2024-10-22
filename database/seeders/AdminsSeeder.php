<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class AdminsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/admins.sql');

        if (!File::exists($path)) {
            $this->command->info("SQL file not found at: $path. Skipping this seeder.");
            return;
        }

        $sql = File::get($path);

        $insertStatements = '';
        preg_match_all('/INSERT INTO `(.+?)`.+?VALUES \((.+?),(.+?)\);/is', $sql, $matches);

        if (!empty($matches[0])) {
            foreach ($matches[0] as $key => $insertStatement) {
                $tableName = $matches[1][$key]; 
                $recordId = trim($matches[2][$key]); 

                DB::unprepared("DELETE FROM `$tableName` WHERE id = '$recordId';");

                $insertStatements .= $insertStatement . "\n";
            }
        }

        if (!empty($insertStatements)) {
            DB::unprepared($insertStatements);
        }
    }
}
