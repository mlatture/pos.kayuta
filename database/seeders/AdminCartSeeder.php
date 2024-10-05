<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdminCartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $path = database_path('seeders/sql/admin_cart.sql');
//
//        if (File::exists($path)) {
//            $sql = File::get($path);
//
//            if (preg_match_all('/INSERT INTO `admin_cart` .*?VALUES\s*\((.*?)\);/is', $sql, $matches)) {
//                $hasData = false;
//
//                foreach ($matches[1] as $values) {
//                    if (trim($values) !== '') {
//                        $hasData = true;
//                        break;
//                    }
//                }
//
//                if ($hasData) {
//                    DB::unprepared($sql);
//                    $this->command->info('Admin cart data seeded from admin_cart.sql');
//                } else {
//                    $this->command->info('No data to insert into admin_cart table. Skipping...');
//                }
//            } else {
//                $this->command->info('No valid INSERT statement found in admin_cart.sql. Skipping...');
//            }
//        } else {
//            $this->command->error('SQL file not found at ' . $path);
//        }
        $path = database_path('seeders/sql/admin_cart.sql');
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
            dd($insertStatements); // For debugging, it will show the SQL queries being executed
        }
    }
}
