<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialMediasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('social_medias')->insert([
            [
                'id' => 1,
                'name' => 'instagram',
                'link' => 'https://www.instagram.com/kayutalakecampground/',
                'icon' => 'ri-instagram-line',
                'active_status' => 1,
                'status' => 1,
                'created_at' => '2023-09-26 17:20:44',
                'updated_at' => '2023-12-13 03:39:19',
            ],
            [
                'id' => 2,
                'name' => 'facebook',
                'link' => 'https://www.facebook.com/KayutaLake',
                'icon' => 'ri-facebook-fill',
                'active_status' => 1,
                'status' => 1,
                'created_at' => '2023-09-26 17:21:24',
                'updated_at' => '2023-10-23 19:20:01',
            ],
            [
                'id' => 3,
                'name' => 'twitter',
                'link' => 'https://twitter.com/kayutalake',
                'icon' => 'ri-twitter-x-line',
                'active_status' => 1,
                'status' => 1,
                'created_at' => '2023-09-26 17:21:49',
                'updated_at' => '2023-12-13 03:37:10',
            ],
            [
                'id' => 4,
                'name' => 'whatsapp',
                'link' => 'web.whatsapp.com',
                'icon' => 'ri-whatsapp-line',
                'active_status' => 0,
                'status' => 0,
                'created_at' => '2023-09-26 17:24:26',
                'updated_at' => '2023-10-23 19:21:07',
            ],
            [
                'id' => 5,
                'name' => 'YouTube',
                'link' => 'https://www.youtube.com/channel/UCXkSh3cZG5R6bd1Oh4WpUpQ',
                'icon' => 'ri-youtube-line',
                'active_status' => 1,
                'status' => 1,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 6,
                'name' => 'Tik Tok',
                'link' => 'https://www.tiktok.com/@kayutalakecampground',
                'icon' => 'ri-facebook-fill',
                'active_status' => 1,
                'status' => 1,
                'created_at' => null,
                'updated_at' => null,
            ]
        ]);
    }
}
