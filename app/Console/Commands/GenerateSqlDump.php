<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class GenerateSqlDump extends Command
{
    protected $signature = 'db:generate-sql-dump';
    protected $description = 'Generate SQL dump for all tables in the database';

    public function handle()
    {
        $tables = DB::select("SHOW TABLES");

        foreach ($tables as $tableObj) {
            $table = (array)$tableObj;
            $tableName = reset($table);

            if (!Schema::hasTable($tableName)) {
                $this->error("Table '$tableName' does not exist.");
                continue;
            }

            $columns = Schema::getColumnListing($tableName);
            $data = DB::table($tableName)->get();

            if ($data->isEmpty()) {
                $this->info("Table '$tableName' is empty. Skipping...");
                continue;
            }

            $sql = "-- Inserting into $tableName table\n";
            $sql .= "INSERT INTO `$tableName` (`" . implode('`, `', $columns) . "`) VALUES\n";

            if ($data->isEmpty()) {
                $sql .= ";\n";
            } else {
                $values = [];
                foreach ($data as $row) {
                    $rowData = [];
                    foreach ($columns as $column) {
                        $rowData[] = is_null($row->$column) ? 'NULL' : "'" . addslashes($row->$column) . "'";
                    }
                    $values[] = "(" . implode(', ', $rowData) . ")";
                }
                $sql .= implode(",\n", $values) . ";\n\n";
            }

            $outputPath = database_path('seeders/sql/' . $tableName . '.sql');

            if (!File::exists(database_path('seeders/sql'))) {
                File::makeDirectory(database_path('seeders/sql'), 0755, true);
            }

            File::put($outputPath, $sql);

            $this->info("SQL dump generated for table '$tableName': $outputPath");
        }
    }
}
