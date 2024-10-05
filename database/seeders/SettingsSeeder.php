<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/settings.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `settings` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Settings data seeded from settings.sql');
                } else {
                    $this->command->info('No data to insert into settings table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in settings.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }

//        $data = [
//            ['key' => 'app_name', 'value' => 'Laravel-POS'],
//            ['key' => 'currency_symbol', 'value' => '$'],
//        ];
//
//        foreach ($data as $value) {
//            Setting::updateOrCreate([
//                'key' => $value['key']
//            ], [
//                'value' => $value['value']
//            ]);
//        }
    }
}
