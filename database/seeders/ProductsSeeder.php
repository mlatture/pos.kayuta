<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        $path = database_path('seeders/sql/products.sql');

        if (!File::exists($path)) {
            $this->command->info("SQL file not found at: $path. Skipping this seeder.");
            return;
        }

        DB::table('products')->truncate();

        $sql = File::get($path);
        $insertStatements = '';
        preg_match_all('/INSERT INTO .+?;/is', $sql, $matches);

        if (!empty($matches[0])) {
            $insertStatements = implode("\n", $matches[0]);
        }

        if (!empty($insertStatements)) {
            DB::unprepared($insertStatements);
        }
    }
}
