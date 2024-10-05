<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class HikingAndMountainBikingMapsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/hiking_and_mountain_biking_maps.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `hiking_and_mountain_biking_maps` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Hiking and Mountain Biking Maps data seeded from hiking_and_mountain_biking_maps.sql');
                } else {
                    $this->command->info('No data to insert into hiking_and_mountain_biking_maps table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in hiking_and_mountain_biking_maps.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }

//        DB::table('hiking_and_mountain_biking_maps')->insert([
//            [
//                'id' => 1,
//                'title' => 'Hiking And Mountain Biking Map',
//                'description' => '<h2>hiking trails,&nbsp;</h2><h2>mountain biking trails,</h2><h2>and maps</h2><ul>
//                    <li><p><a href="https://www.kayuta.com/bald-mountain" target="_self">Bald Mountain</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/bear-lake" target="_self">Bear Lake</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/black-bear-mountain" target="_self">Black Bear Mountain</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/black-river-canal" target="_self">Black River Canal</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/brewer-lake" target="_self">Brewer Lake</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/bubb-sis-moss-lake-loop-vista-trail" target="_self">Bubb, Sis &amp; Moss Lake Loop &amp; Vista Trail</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/fern-park-trails" target="_self">Fern Park Trails</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/fopo-to-blue-line" target="_self">Forestport to the Adirondack Park Blue Line</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/gull-lake" target="_self">Gull Lake</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/hiking-inlet" target="_self">Hiking Inlet</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/mountain-bike-inlet" target="_self">Mountain Bike Inlet</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/mountain-bike-train-route" target="_self">Mountain Bike Train Route</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/nicks-lake-loop" target="_self">Nicks Lake Loop</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/pixley-falls-state-park-to-boonvill" target="_self">Pixley Falls State Park to Boonville</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/remsen-falls" target="_self">Remsen Falls</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/rocky-mountain" target="_self">Rocky Mountain</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/round-pond" target="_self">Round Pond</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/sand-lake-falls" target="_self">Sand Lake Falls &amp; Stone Dam Lake</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/tobie-trail" target="_self">TOBIE Trail</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/woodhull-lake" target="_self">Woodhull Lake</a></p></li>
//                    <li><p><a href="https://www.kayuta.com/woodhull-mountain" target="_self">Woodhull Mountain</a></p></li>
//                </ul>',
//                'status' => 1,
//                'created_at' => '2023-09-30 06:38:30',
//                'updated_at' => '2023-11-05 14:45:19',
//            ]
//        ]);
    }
}
