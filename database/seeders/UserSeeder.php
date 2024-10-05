<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/users.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `users` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Users data seeded from users.sql');
                } else {
                    $this->command->info('No data to insert into users table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in users.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }

//        User::updateOrCreate([
//            'email' => 'admin@mail.com'
//        ], [
//            'name' => 'Admin CA',
//            'f_name' => 'Admin',
//            'l_name' => 'CA',
//            'email'=>'admin@mail.com',
//            'password' => bcrypt('password')
//        ]);
    }
}
