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

        if (!preg_match('/INSERT INTO `admins` \(([^)]+)\) VALUES/is', $sql, $columnsMatch)) {
            $this->command->error("Failed to parse column names from the SQL file.");
            return;
        }

        $columnNames = array_map('trim', explode(',', $columnsMatch[1]));

        if (!preg_match_all('/\(([^)]+)\)(,|;)/s', $sql, $rows)) {
            $this->command->error("No values found to insert.");
            return;
        }

        foreach ($rows[1] as $row) {
            $columns = preg_split('/,(?=(?:[^\']*\'[^\']*\')*[^\']*$)/', $row);
            $columns = array_map(fn($value) => trim($value, " '"), $columns);

            if (count($columnNames) !== count($columns)) {
                $this->command->error("Column count mismatch for row: ($row)");
                continue;
            }

            $data = array_combine($columnNames, $columns);

            $data = array_map(fn($value) => $value === 'NULL' ? null : $value, $data);

            $exists = DB::table('admins')
                ->where('email', $data['email'] ?? null)
                ->where('created_at', $data['created_at'] ?? null)
                ->exists();

            if (!$exists) {
                DB::table('admins')->insert($data);
            }
        }

        $this->command->info('Admins table seeded successfully!');
    }
}
