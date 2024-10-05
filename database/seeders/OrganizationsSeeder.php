<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class OrganizationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/organizations.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `organizations` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Organizations data seeded from organizations.sql');
                } else {
                    $this->command->info('No data to insert into organizations table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in organizations.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }

//        $data = [
//            [
//                'id' => 1,
//                'name' => 'sds',
//                'address_1' => 'kjsfdjsj',
//                'address_2' => 'sjfksjfs',
//                'city' => 'jadjad',
//                'state' => 'wrwrw',
//                'zip' => '34342',
//                'country' => 'USA',
//                'status' => 'Active',
//                'created_at' => '2024-05-04 10:59:43',
//                'updated_at' => '2024-05-04 10:59:43',
//            ],
//            [
//                'id' => 2,
//                'name' => 'kill',
//                'address_1' => 'bill',
//                'address_2' => 'hill',
//                'city' => 'New York',
//                'state' => 'Alaska',
//                'zip' => '10001',
//                'country' => 'USA',
//                'status' => 'Active',
//                'created_at' => '2024-05-05 02:31:21',
//                'updated_at' => '2024-05-05 02:31:21',
//            ],
//        ];
//
//        DB::table('organizations')->insert($data);
    }
}
