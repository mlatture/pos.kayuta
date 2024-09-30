<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('events')->insert([
            [
                'id' => 5,
                'eventname' => 'Mother’s Day Weekend 2024!',
                'eventstart' => '2024-05-10',
                'eventend' => '2024-05-12',
                'minimumstay' => 1,
                'bookingmessage' => null,
                'description' => '<p>Mother’s Day Weekend!</p><p>Paying tribute to Mothers!</p><p>&nbsp;</p><p><strong>Saturday, May 11</strong></p><p>10:30 AM Craft: Make a Mother’s Day flowerpot and greeting card. In The PlayHouse</p><p>‘<strong>Sunday, May 12</strong></p><p>9-10:30 AM Mother’s Day Pancake Breakfast (while supplies last). In The PlayHouse</p>',
                'embeddedvideo' => null,
                'extracharge' => null,
                'extranightlycharge' => null,
                'poster' => '2024-01-24-65b1ea4f8207f.png',
                'previewdescription' => null,
                'eventcode' => 'MOTHER',
                'headergraphic' => null,
                'lastmodified' => '2024-01-25 04:59:27',
                'created_at' => '2023-11-05 14:17:29',
                'updated_at' => '2024-01-24 23:59:27',
            ],
            [
                'id' => 7,
                'eventname' => 'Fourth of July',
                'eventstart' => '2024-07-04',
                'eventend' => '2024-07-07',
                'minimumstay' => 3,
                'bookingmessage' => 'Three night minimum stay required',
                'description' => null,
                'embeddedvideo' => null,
                'extracharge' => null,
                'extranightlycharge' => null,
                'poster' => '2024-01-18-65a9b74a5bfbe.png',
                'previewdescription' => null,
                'eventcode' => null,
                'headergraphic' => null,
                'lastmodified' => '2024-01-24 02:54:20',
                'created_at' => '2023-12-19 05:55:10',
                'updated_at' => '2024-01-23 21:54:20',
            ],
            [
                'id' => 8,
                'eventname' => 'Rest and Relax Weekend!',
                'eventstart' => '2024-05-18',
                'eventend' => '2024-05-18',
                'minimumstay' => null,
                'bookingmessage' => null,
                'description' => '<p>Rest and Relax Weekend!</p><p>Saturday, May 18</p><p>12:00 to 1:30 Make Your Own 3 scoop Ice Cream Float or Sundae! $5,&nbsp;Pup Cup for your doggie &ndash; 1 scoop of vanilla ice cream $2, At the Camp Store</p><p>4:00 PM Hay Wagon Ride! Loading and unloading in the grass next to the swimming pool</p><p>Archery and Laser Tag are offered at other dates and times by special request.</p>',
                'embeddedvideo' => null,
                'extracharge' => null,
                'extranightlycharge' => null,
                'poster' => '2024-01-23-65b07ca2d5e57.png',
                'previewdescription' => null,
                'eventcode' => null,
                'headergraphic' => null,
                'lastmodified' => '2024-01-24 02:57:38',
                'created_at' => '2024-01-23 21:57:38',
                'updated_at' => '2024-01-23 21:57:38',
            ],
            [
                'id' => 9,
                'eventname' => 'Memorial Day Weekend!',
                'eventstart' => '2024-05-24',
                'eventend' => '2024-05-26',
                'minimumstay' => null,
                'bookingmessage' => null,
                'description' => '<p>Memorial Day Weekend!</p><p>Sunflower Seed Spitting Contest, $6, $8, and $10 camp store gift cards and candy will be awarded!</p><p>Honoring the men and women who died while serving in the U.S. military. The Memorial Day parade will line up in front of the swimming pool.&nbsp; Line up with your bikes, golf carts, or just walk along! Bring your bikes and golf carts down early to decorate them for the parade!</p><p>&nbsp;</p><p>Friday, May 24</p><p>9:00 PM Glow Stick Hay Wagon Ride! &ndash; Bring something that glows or blinks! Loading and unloading in the grass next to the swimming pool</p><p>Saturday, May 25</p><p>10:30 AM Contest: Sunflower Seed Spitting Competition, Kid and adult leagues. $6, $8, and $10 camp store gift cards and candy will be awarded! In The PlayHouse</p><p>12:00 to 1:30 Make Your Own 3 scoop Ice Cream Float or Sundae! $5, Pup Cup for your doggie &ndash; 1 scoop of vanilla ice cream $2, At the Camp Store</p><p>1:00 PM Archery - Kids and Adults! Sign up At the Camp Store by 12:30 - $10</p><p>2:30 PM Laser Tag - Kids and Adults! Sign up At the Camp Store by 1:30 - $15</p><p>4:00 PM Hay Wagon Ride! Loading and unloading in the grass next to the swimming pool</p><p>7:00-9:30 PM Live&nbsp; Band “Scot Raymond”. In the PlayHouse</p><p>9:00 PM Glow Stick Hay Wagon Ride! &ndash; Bring something that glows or blinks!&nbsp;Loading and unloading in the grass next to the swimming pool</p><p>&nbsp;</p><p>Sunday, May 26</p><p>10:00 AM Decorate your bike or golf cart and get ready for the parade.&nbsp;In front of the swimming pool</p><p>11:00 AM Memorial Day Parade. line up near the pool to be in the parade! Ride your bikes, golf carts, or just walk along!</p><p>12:00 to 1:30 Make Your Own 3 scoop Ice Cream Float or Sundae! $5&nbsp;Pup Cup for your doggie &ndash; 1 scoop of vanilla ice cream $2.&nbsp;At the Camp Store</p><p>1:00 PM Archery - Kids and Adults! Sign up At the Camp Store by 12:30 - $10</p><p>2:30 PM Laser Tag - Kids and Adults! Sign up At the Camp Store by 1:30 - $15</p><p>4:00 PM Hay Wagon Ride! Loading and unloading in the grass next to the swimming pool</p><p>9:00 PM Glow Stick Hay Wagon Ride! &ndash; Bring something that glows or blinks! Loading and unloading in the grass next to the swimming pool</p><p>&nbsp;</p><p>Archery and Laser Tag are offered at other dates and times by special request.</p><p>Every day: Loud radios, loud talking, or other noises are not permitted during the day.</p><p>Quiet hours begin at 10:00 PM &ndash; no radios or loud talking.</p>',
                'embeddedvideo' => null,
                'extracharge' => null,
                'extranightlycharge' => null,
                'poster' => '2024-01-23-65b07dab3a54b.png',
                'previewdescription' => null,
                'eventcode' => null,
                'headergraphic' => null,
                'lastmodified' => '2024-01-24 03:02:03',
                'created_at' => '2024-01-23 22:02:03',
                'updated_at' => '2024-01-23 22:02:03',
            ],
            [
                'id' => 9,
                'eventname' => 'Memorial Day Weekend!',
                'eventstart' => '2024-05-24',
                'eventend' => '2024-05-26',
                'minimumstay' => null,
                'bookingmessage' => null,
                'description' => '<p>Memorial Day Weekend!</p><p>Sunflower Seed Spitting Contest, $6, $8, and $10 camp store gift cards and candy will be awarded!</p><p>Honoring the men and women who died while serving in the U.S. military. The Memorial Day parade will line up in front of the swimming pool.&nbsp; Line up with your bikes, golf carts, or just walk along! Bring your bikes and golf carts down early to decorate them for the parade!</p><p>&nbsp;</p><p>Friday, May 24</p><p>9:00 PM Glow Stick Hay Wagon Ride! &ndash; Bring something that glows or blinks! Loading and unloading in the grass next to the swimming pool</p><p>Saturday, May 25</p><p>10:30 AM Contest: Sunflower Seed Spitting Competition, Kid and adult leagues. $6, $8, and $10 camp store gift cards and candy will be awarded! In The PlayHouse</p><p>12:00 to 1:30 Make Your Own 3 scoop Ice Cream Float or Sundae! $5, Pup Cup for your doggie &ndash; 1 scoop of vanilla ice cream $2, At the Camp Store</p><p>1:00 PM Archery - Kids and Adults! Sign up At the Camp Store by 12:30 - $10</p><p>2:30 PM Laser Tag - Kids and Adults! Sign up At the Camp Store by 1:30 - $15</p><p>4:00 PM Hay Wagon Ride! Loading and unloading in the grass next to the swimming pool</p><p>7:00-9:30 PM Live&nbsp; Band “Scot Raymond”. In the PlayHouse</p><p>9:00 PM Glow Stick Hay Wagon Ride! &ndash; Bring something that glows or blinks!&nbsp;Loading and unloading in the grass next to the swimming pool</p><p>&nbsp;</p><p>Sunday, May 26</p><p>10:00 AM Decorate your bike or golf cart and get ready for the parade.&nbsp;In front of the swimming pool</p><p>11:00 AM Memorial Day Parade. line up near the pool to be in the parade! Ride your bikes, golf carts, or just walk along!</p><p>12:00 to 1:30 Make Your Own 3 scoop Ice Cream Float or Sundae! $5&nbsp;Pup Cup for your doggie &ndash; 1 scoop of vanilla ice cream $2.&nbsp;At the Camp Store</p><p>1:00 PM Archery - Kids and Adults! Sign up At the Camp Store by 12:30 - $10</p><p>2:30 PM Laser Tag - Kids and Adults! Sign up At the Camp Store by 1:30 - $15</p><p>4:00 PM Hay Wagon Ride! Loading and unloading in the grass next to the swimming pool</p><p>9:00 PM Glow Stick Hay Wagon Ride! &ndash; Bring something that glows or blinks! Loading and unloading in the grass next to the swimming pool</p><p>&nbsp;</p><p>Archery and Laser Tag are offered at other dates and times by special request.</p><p>Every day: Loud radios, loud talking, or other noises are not permitted during the day.</p><p>Quiet hours begin at 10:00 PM &ndash; no radios or loud talking.</p>',
                'embeddedvideo' => null,
                'extracharge' => null,
                'extranightlycharge' => null,
                'poster' => '2024-01-23-65b07dab3a54b.png',
                'previewdescription' => null,
                'eventcode' => null,
                'headergraphic' => null,
                'lastmodified' => '2024-01-24 03:02:03',
                'created_at' => '2024-01-23 22:02:03',
                'updated_at' => '2024-01-23 22:02:03',
            ],

        ]);
    }
}
