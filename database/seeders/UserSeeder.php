<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate([
            'email' => 'admin@mail.com'
        ], [
            'name' => 'Admin CA',
            'f_name' => 'Admin',
            'l_name' => 'CA',
            'email'=>'admin@mail.com',
            'password' => bcrypt('password')
        ]);
    }
}
