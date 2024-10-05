<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class DirectionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/directions.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `directions` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Directions data seeded from directions.sql');
                } else {
                    $this->command->info('No data to insert into directions table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in directions.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }

//        DB::table('directions')->insert([
//            [
//                'id' => 1,
//                'title' => 'Directions',
//                'description' => '<h3>GPS Notes:</h3>
//                    <p>When coming down O\'Brien Rd, ignore your GPS if it tells you to turn right on Kayuta Terrace. This is a dead end with NO campground access and no means to turn around. Keep going straight for another 500\' to Campground Road and make a right turn there. You will see our campground sign on the corner.<br />
//                    Also, when coming down Bardwell Mills Road, ignore your GPS if it tells you to turn right onto Lake Julia Road/Ebert Road. Instead, turn left. Go 1.4 miles and then turn right onto Campground Road.<br />
//                    Turn Right at the stop sign to go to the office.<br />
//                    RVs, campers, and trailers should stop at the “Stop Here” sign for check-in.</p>
//
//                    <h3>From The South:</h3>
//                    <p>Take RT-12 North into Utica. Once through Utica, continue on RT-12 North for 20 miles.<br />
//                    Turn right onto Dayton Road (at Evans Equipment tractor dealer) and go 2.3 miles.<br />
//                    Curve to the right onto Brown Tract Road and go .1 of a mile.<br />
//                    Immediately turn left onto Bardwell Mills Road and go .7 of a mile.<br />
//                    Turn left onto Lake Julia Road/Ebert Road (which changes name to O Brien Road) and go 1.4 miles.<br />
//                    Turn right onto Campground Road (Do NOT turn onto Kayuta Terrace).<br />
//                    Turn Right at the stop sign to go to the office.<br />
//                    RVs, campers, and trailers should stop at the “Stop Here” sign for check-in.</p>
//
//                    <h3>From The North:</h3>
//                    <p>From RT-12:<br />
//                    Merge onto RT-28 North. Follow RT-28 North for 2.1 miles. Just over the steel bridge, take the Woodhull Rd (CR-72) exit. Bear right off the ramp onto Woodhull Rd. Follow Woodhull Rd for 1.2 miles to the Buffalo Head Restaurant. Bear right at the Buffalo head on Bardwell Mills Rd. Follow Bardwell Mills Rd for 2 miles to Kayuta Lake Campground on the left.<br />
//                    Turn Right at the stop sign to go to the office.<br />
//                    RVs, campers, and trailers should stop at the “Stop Here” sign for check-in.<br />
//                    From RT-28:<br />
//                    Once in Forestport, just before the steel bridge, take the Woodhull Rd (CR-72) exit. Bear right and go under the RT-28 bridge. Follow Woodhull Rd for 1.2 miles to the Buffalo Head Restaurant. Bear right at the Buffalo head on Bardwell Mills Rd. Follow Bardwell Mills Rd for 2 miles to Kayuta Lake Campground on the left.<br />
//                    Turn Right at the stop sign to go to the office.<br />
//                    RVs, campers, and trailers should stop at the “Stop Here” sign for check-in.</p>
//
//                    <h3>From The West:</h3>
//                    <p>Take I-90 East to Exit 33 - RT-365 E toward Verona/Rome. Continue on RT-365 for the next 15 miles into Barneveld to RT-12 North. Take RT-12 North for 6.9 miles.<br />
//                    Turn right onto Dayton Road (at Evans Equipment tractor dealer) and go 2.3 miles.<br />
//                    Curve to the right onto Brown Tract Road and go .1 of a mile.<br />
//                    Immediately turn left onto Bardwell Mills Road and go .7 of a mile.<br />
//                    Turn left onto Lake Julia Road/Ebert Road (which changes name to O Brien Road) and go 1.4 miles.<br />
//                    Turn right onto Campground Road (Do NOT turn onto Kayuta Terrace).<br />
//                    Turn Right at the stop sign to go to the office.<br />
//                    RVs, campers, and trailers should stop at the “Stop Here” sign for check-in.</p>
//
//                    <h3>From The East:</h3>
//                    <p>From Utica (off NY Thruway 90) take Exit 31 - toward RT-8 / RT-12 / UTICA. Take RT-12 North for 19.4 miles.<br />
//                    Turn right onto Dayton Road (at Evans Equipment tractor dealer) and go 2.3 miles.<br />
//                    Curve to the right onto Brown Tract Road and go .1 of a mile.<br />
//                    Immediately turn left onto Bardwell Mills Road and go .7 of a mile.<br />
//                    Turn left onto Lake Julia Road/Ebert Road (which changes name to O Brien Road) and go 1.4 miles.<br />
//                    Turn right onto Campground Road (Do NOT turn onto Kayuta Terrace).<br />
//                    Turn Right at the stop sign to go to the office.<br />
//                    RVs, campers, and trailers should stop at the “Stop Here” sign for check-in.</p>',
//                'latitude' => '43.40955',
//                'longitude' => '-75.186758',
//                'pdf' => '2023-09-28-65155327a23ac.pdf',
//                'status' => 1,
//                'created_at' => '2023-09-28 05:06:43',
//                'updated_at' => '2023-09-28 05:19:19',
//            ],
//        ]);
    }
}
