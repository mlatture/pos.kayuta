<?php

namespace Database\Seeders;

use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class WhitelistTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        try {
            $path = database_path('seeders/sql/whitelist_tables.sql');

            if (!File::exists($path)) {
                $this->command->info("SQL file not found at: $path. Skipping this seeder.");
                return;
            }

            $sql = File::get($path);
            $insertStatements = '';
            preg_match_all('/INSERT INTO .+?;/is', $sql, $matches);

            if (!empty($matches[0])) {
                $insertStatements = implode("\n", $matches[0]);
            }

            if (!empty($insertStatements)) {
                DB::unprepared($insertStatements);
            }
        } catch (QueryException $e) {
            logger($e->getMessage());
        }
    }
}
