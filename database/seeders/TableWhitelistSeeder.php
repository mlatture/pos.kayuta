<?php

namespace Database\Seeders;

use App\Models\WhitelistTable;
use Doctrine\DBAL\Exception;
use Illuminate\{Database\Seeder, Support\Facades\DB};

class TableWhitelistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        WhitelistTable::truncate();

        $data = [];
        foreach (DB::connection()->getDoctrineSchemaManager()->listTableNames() as $table) {
            if (in_array($table, ['translations', 'trailer_toung_weights', 'whitelist_tables', 'dictionary_tables'])) {
                continue;
            }
            $data[] = ['table_name' => $table, 'read_permission_level' => 1, 'update_permission_level' => 1, 'delete_permission_level' => 0];
        }
        WhitelistTable::insert($data);
    }
}
