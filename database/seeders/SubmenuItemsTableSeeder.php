<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class SubmenuItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/submenu_items.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `submenu_items` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Submenu Items data seeded from submenu_items.sql');
                } else {
                    $this->command->info('No data to insert into submenu_items table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in submenu_items.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }

//        DB::table('submenu_items')->insert([
//            [
//                'id' => 11,
//                'menu_item_id' => 1,
//                'title' => 'Chatgpt',
//                'url' => 'https://chatgpt.com/c/f427c4f9-9a08-4f89-ac84-37ca65595faf',
//                'target' => 'window',
//                'order' => 0,
//                'created_at' => '2024-08-06 02:48:45',
//                'updated_at' => '2024-08-06 03:34:41',
//            ],
//            [
//                'id' => 12,
//                'menu_item_id' => 1,
//                'title' => 'Pinterest',
//                'url' => 'https://www.pinterest.com/allisonlester90/pretty-pictures/',
//                'target' => 'window',
//                'order' => 0,
//                'created_at' => '2024-08-06 02:48:54',
//                'updated_at' => '2024-08-06 03:26:55',
//            ]
//        ]);
    }
}
