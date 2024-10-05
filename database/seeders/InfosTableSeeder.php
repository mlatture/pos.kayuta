<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class InfosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders/sql/infos.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            if (preg_match('/INSERT INTO `infos` .* VALUES\s*\(([^)]+)\);/', $sql, $matches)) {
                if (trim($matches[1]) !== '') {
                    DB::unprepared($sql);
                    $this->command->info('Infos data seeded from infos.sql');
                } else {
                    $this->command->info('No data to insert into infos table. Skipping...');
                }
            } else {
                $this->command->info('No INSERT statement found in infos.sql. Skipping...');
            }
        } else {
            $this->command->error('SQL file not found at ' . $path);
        }

//        DB::table('infos')->insert([
//            [
//                'id' => 2,
//                'title' => 'Check In / Check Out',
//                'description' => '<p>Check-In: 2:00 PM<br />Check Out: 1:00 PM for Sites &amp; 11:00 AM for Cabins</p><p>Early check-in and late check-out are not guaranteed. Your site may be occupied by another party the night before your arrival or the day of your departure. Please check with the office first if you plan on arriving before 2 PM or checking out after 1 PM. Sorry, we do not allow early check in’s or late check out’s for rental cabins or rental campers.</p>',
//                'status' => 1,
//                'created_at' => '2023-09-25 16:05:28',
//                'updated_at' => '2023-09-25 17:02:24',
//                'show_in_details' => 0,
//            ],
//            [
//                'id' => 3,
//                'title' => 'Cancelations / Date Changes',
//                'description' => '<p>Cancellations 14 days or more prior to arrival date are subject to a 15% cancellation fee.<br />There are no refunds for cancellations within 14 days prior to the arrival date.<br />There are no date changes allowed within 14 days prior to the arrival date.<br />There are no refunds in the event of forced closures due to COVID, other diseases, disasters, or due to other reasons.<br />For stays that qualify, only rain checks will be issued in the event of forced closure.<br />There are no refunds or discounts if an amenity, activity, or event is not available, closed, or canceled.</p>',
//                'status' => 1,
//                'created_at' => '2023-09-25 16:08:57',
//                'updated_at' => '2023-09-25 16:08:57',
//                'show_in_details' => 0,
//            ],
//            [
//                'id' => 4,
//                'title' => 'Quiet Nature of the Park During All Hours',
//                'description' => '<p>Nobody may disturb the quiet nature of the park at any time.<br />During the day loud sounds from items such as radios, televisions, audio equipment, loud talking, or other loud noises are prohibited. These must be kept within reason.<br />Quiet Hours are from 10:00 PM to 8:00 AM. Radios, televisions, audio equipment, loud talking, or other noises are not allowed at all during this time. Cornhole and other outdoor games are prohibited during this time.<br />If you are hosting a campfire/get-together, you are responsible for keeping the group quiet enough to not disturb your neighbors.<br />Children, including teens, must be back on their site by 10 pm unless accompanied by a parent.<br />No golf cart use after 10 pm. Please try to minimize traffic through the park during these hours.<br />You may NOT interfere with others’ peaceful enjoyment of the Campground at any time.</p>',
//                'status' => 1,
//                'created_at' => '2023-09-25 16:09:45',
//                'updated_at' => '2023-12-13 12:23:49',
//                'show_in_details' => 0,
//            ],
//            [
//                'id' => 5,
//                'title' => 'Rental Cabins And Rental Campers',
//                'description' => '<p>While rental cabins and rental campers can sleep 5, no more than 3 adults are allowed at one time.<br />Rental cabins, screen rooms, and rental campers are non-smoking.<br />No pets are allowed in the rental cabins, screen rooms, or in the rental campers.<br />Please bring your own bed and bath linens as we do not provide them.<br />Your credit and/or debit card will automatically be charged a cleaning fee of up to $200 for smoking in rental cabins or campers or for bringing in pets.Your credit and/or debit card will also be automatically charged an additional cleaning fee of up to $250 for trash or cigarette butts left behind and/or for burning trash.<br />Your credit and/or debit card will be automatically charged up to $3,000 for disfiguring any tree, dead or alive.<br />Sorry, we do not allow early check in’s or late check out’s for rental cabins or rental campers.<br />All Cabin and Camper rentals are subject to 8.75% NYS Sales Tax.</p>',
//                'status' => 1,
//                'created_at' => '2023-09-25 16:11:12',
//                'updated_at' => '2023-09-25 16:11:12',
//                'show_in_details' => 0,
//            ],
//            [
//                'id' => 6,
//                'title' => 'Minimum Stay Requirements',
//                'description' => '<p>ALL campsites &amp; rental units have a 3-night minimum stay on holidays.Holiday dates include:</p><ul><li>Memorial Day</li><li>July 4th</li><li>Woodsman Field Days</li><li>Labor Day</li></ul><p>Rental Units on non-holidays require a (2) night minimum reservation.</p>',
//                'status' => 1,
//                'created_at' => '2023-09-25 16:11:49',
//                'updated_at' => '2023-09-25 16:11:49',
//                'show_in_details' => 0,
//            ],
//            [
//                'id' => 7,
//                'title' => 'Camping Clubs',
//                'description' => '<p>Camping clubs are welcome. Camping clubs that reserve 10 or more campsites for 2 or more nights will receive our Camping Club Discount. The pavilion is available for club activities. Discount does not apply on Holiday or Woodsmen Field Days weekends.</p><p>10-15 Units – 15% Discount<br />16-20 Units – 20% Discount</p>',
//                'status' => 1,
//                'created_at' => '2023-09-25 16:12:27',
//                'updated_at' => '2023-09-25 16:12:27',
//                'show_in_details' => 0,
//            ],
//            [
//                'id' => 8,
//                'title' => 'Discounts',
//                'description' => '<p>Discounts do not apply to holidays, special event weekends or rates that are already discounted.<br />Military/Hero specials are only available during Heroes’ Weekend.<br />Please present all coupons, vouchers, discount cards or Military/Hero ID upon check-in.</p>',
//                'status' => 1,
//                'created_at' => '2023-09-25 16:12:58',
//                'updated_at' => '2023-09-25 16:12:58',
//                'show_in_details' => 0,
//            ],
//            [
//                'id' => 9,
//                'title' => 'Site Lock Fee Guarantee',
//                'description' => '<p>While we guarantee your site type, our automated optimization system may change your site location. You can guarantee your chosen site with a lock fee.</p><p>If your booked and locked site becomes unavailable, we will transfer your reservation to the closest site of the same site type and refund your camping fee.</p>',
//                'status' => 1,
//                'created_at' => '2023-09-25 16:13:30',
//                'updated_at' => '2023-09-25 16:13:30',
//                'show_in_details' => 0,
//            ],
//            [
//                'id' => 10,
//                'title' => 'General',
//                'description' => '<p>Rates are based on 4 people and one camping unit per site – or 5 people per cabin.Additional overnight guests are extra per person per night.One small 2-man tent (7\'x7\') may be set up on campsites for the children to sleep in. Tents are not allowed on Cabin sites.Children under 6 are FREE but count in the total number of people on the site.<br />Your rig length is measured from the tow hitch to the back bumper.<br />All rates are subject to change without notice.</p><p>To reduce overcrowding, there is a 6-person maximum per site, regardless of age. Groups that exceed these numbers MUST rent additional campsites or cabins.</p><p>Anyone visiting a site who did not register with the original party must pay a visitor fee.To reduce overcrowding and ensure your safety, visitor numbers may be limited.</p>',
//                'status' => 1,
//                'created_at' => '2023-09-25 16:13:58',
//                'updated_at' => '2023-09-25 16:13:58',
//                'show_in_details' => 0,
//            ],
//        ]);
    }
}
