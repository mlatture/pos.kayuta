<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
        $data = [
            [
                'id' => 1,
                'name' => 'admin',
                'phone' => '+92 303030333',
                'admin_role_id' => 1,
                'image' => 'def.png',
                'email' => 'admin@kayuta-lake.com',
                'email_verified_at' => null,
                'password' => '$2a$12$VpoliA7ep8WezOBo.RJY0OKrKOIVwUh8e5loBGoYUr0qpdBfo38Xe',
                'remember_token' => 'GQw3ElW6e9IkNSkb5ZyYGmbWehBOOA3PjAjklqoGhUUO4FQ8vcfIgojrD6HQ',
                'created_at' => '2023-05-05 03:57:00',
                'updated_at' => '2023-05-05 03:57:00',
                'status' => 1,
            ],
            [
                'id' => 2,
                'name' => 'employee 1',
                'phone' => '233432423',
                'admin_role_id' => 7,
                'image' => '2023-05-05-64546e81b6939.png',
                'email' => 'manager@example.com',
                'email_verified_at' => null,
                'password' => '$2y$10$M/.U5GhbeYvfH3AIhm7B3uU2V4dqMyqVyX9Z.KSGAsMSC68/cRfX6',
                'remember_token' => null,
                'created_at' => '2023-05-05 15:48:33',
                'updated_at' => '2023-05-05 15:48:33',
                'status' => 1,
            ],
            [
                'id' => 4,
                'name' => 'john',
                'phone' => '09876543211',
                'admin_role_id' => 7,
                'image' => '2023-12-13-6579bd878deea.png',
                'email' => 'john@gmail.com',
                'email_verified_at' => null,
                'password' => '$2y$10$dkEejUt.PoKcvRksPUz83.r0ZFgCGLzrNZ6/.GogOAlgGdrhTEkTy',
                'remember_token' => null,
                'created_at' => '2023-12-13 09:19:51',
                'updated_at' => '2023-12-13 09:19:51',
                'status' => 1,
            ],
            [
                'id' => 5,
                'name' => 'Abrar khan',
                'phone' => '123132213',
                'admin_role_id' => 9,
                'image' => '2023-12-13-6579bdcc74a6f.png',
                'email' => 'site@gmail.com',
                'email_verified_at' => null,
                'password' => '$2y$10$l74aeT4VO8J0c1TWsUXiqeJ9XAy4XpN6MSsPEozQA0EBSxkkEtwk2',
                'remember_token' => null,
                'created_at' => '2023-12-13 09:21:00',
                'updated_at' => '2023-12-13 09:21:00',
                'status' => 1,
            ],
            [
                'id' => 6,
                'name' => 'Sky Lake',
                'phone' => '6143603013',
                'admin_role_id' => 8,
                'image' => '2023-12-13-657a0b0c0b312.png',
                'email' => 'info@kayuta.com',
                'email_verified_at' => null,
                'password' => '$2y$10$ZqHn2WxHMzDhFSzI0fofD.UOUI2YDg/REOG6lKkoXgCiz3cew5f66',
                'remember_token' => null,
                'created_at' => '2023-12-13 14:50:36',
                'updated_at' => '2023-12-13 14:50:36',
                'status' => 1,
            ],
        ];

        DB::table('admins')->insert($data);
    }
}
