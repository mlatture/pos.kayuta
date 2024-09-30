<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocalAreaAttractionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('local_area_attractions')->insert([
            'id' => 1,
            'title' => 'Local Area Attractions',
            'description' => '<h2>local area attractions</h2>
                <p>Old Forge (29mi/40min)</p>
                <ul>
                    <li><p><a href="http://www.watersafari.com/" target="_self">Enchanted Forest / Water Safari</a></p></li>
                    <li><p><a href="https://oldforgelakecruises.com/" target="_self">Old Forge Lake Cruises</a></p></li>
                    <li><p><a href="https://www.ae-adventures.com/" target="_self">Whitewater Rafting</a></p></li>
                    <li><p><a href="http://mccauleyny.com/chair_lift.html" target="_self">McCauley Mountain - Scenic Chairlift Ride</a></p></li>
                </ul>
                <p>Blue Mountain Lake (63mi/1hr25min)</p>
                <ul>
                    <li><p><a href="https://www.theadkx.org/" target="_self">The Adirondack Museum</a></p></li>
                </ul>
                <p>Boonville (19mi/27min)</p>
                <ul>
                    <li><p><a href="https://www.parks.ny.gov/parks/32/details.aspx" target="_self">Hike Pixley Falls State Park</a></p></li>
                </ul>
                <p>Bald Mountain (33mi/43min)</p>
                <ul>
                    <li><p><a href="https://www.cnyhiking.com/BaldMountain.htm" target="_self">Hike Bald Mountain</a></p></li>
                </ul>
                <p>Trenton (15mi/19min) (Seasonal)</p>
                <ul>
                    <li><p><a href="https://towntrenton.digitaltowpath.org:10031/content/Parks/View/4" target="_self">Hike Trenton Falls</a></p></li>
                </ul>
                <p>Verona (37mi/48min)</p>
                <ul>
                    <li><p><a href="https://www.turningstone.com/" target="_blank">Turning Stone Casino</a></p></li>
                </ul>
                <p>Herkimer (29mi/42min)</p>
                <ul>
                    <li><p><a href="https://www.herkimerdiamond.com/herkimer-diamond-mines/" target="_self">The Herkimer Diamond Mines</a></p></li>
                </ul>
                <p>Golfing at area golf courses such as:</p>
                <ul>
                    <li><p><a href="http://www.frontiernet.net/~aldcrkgc/" target="_self">Alder Creek Golf Course</a></p></li>
                    <li><p><a href="http://www.romecountryclub.com/" target="_self">Rome Country Club</a></p></li>
                    <li><p><a href="https://www.thendaragolfclub.com/" target="_self">Thendara Golf Club</a></p></li>
                    <li><p><a href="http://woodgatepines.com/" target="_self">Woodgate Pines</a></p></li>
                </ul>
                <p>Utica (27mi/34min)</p>
                <ul>
                    <li><p><a href="http://uticazoo.org/" target="_self">Utica Zoo</a></p></li>
                    <li><p><a href="https://www.saranac.com/" target="_self">Saranac Brewing Company</a></p></li>
                    <li><p><a href="https://www.adirondackrr.com/" target="_self">Adirondack Scenic Railroad</a></p></li>
                    <li><p><a href="https://www.woodlandbeer.com/" target="_self">Woodland Farm Brewery</a></p></li>
                    <li><p><a href="https://www.mwpai.org/" target="_self">Munson-Williams-Proctor Arts Institute</a></p></li>
                    <li><p><a href="https://www.thestanley.org/" target="_self">Stanley Center for The Arts</a></p></li>
                    <li><p><a href="https://playersofutica.org/" target="_self">Players of Utica</a>&nbsp;&ndash; Utica New York\'s Community Theater</p></li>
                    <li><p><a href="http://uticacm.org/" target="_self">Utica Children\'s Museum</a></p></li>
                </ul>
                <p>Sylvan Beach (47mi/1hr)</p>
                <ul>
                    <li><p><a href="https://www.sylvanbeachamusementpark.com/" target="_self">Sylvan Beach Amusement Park</a></p></li>
                </ul>
                <p>Rome (24mi/37min)</p>
                <ul>
                    <li><p><a href="https://www.nps.gov/fost/index.htm" target="_self">Fort Stanwix National Monument</a></p></li>
                    <li><p><a href="http://www.dec.ny.gov/outdoor/7742.html" target="_self">Rome Fish Hatchery</a>&nbsp;- 8306 Fish Hatchery Rd, Rome, NY</p></li>
                </ul>
                <p>Turin (26mi/37min)</p>
                <ul>
                    <li><p><a href="http://www.stillmeadowranch.com/" target="_self">Stillmeadow Ranch</a>&nbsp;- Horseback riding</p></li>
                </ul>
                <p>There are many great restaurants in the area.<br />Just up the road from the campground are:</p>
                <ul>
                    <li><p>Garramone\'s Restaurant (Italian)</p></li>
                    <li><p>The Forestport Diner</p></li>
                    <li><p>The Wigwam Tavern</p></li>
                    <li><p>Kratzy\'s Bar &amp; Grill</p></li>
                    <li><p>Campbell\'s Homestyle Cook Inn.</p></li>
                    <li><p>The Soda Fountain</p></li>
                </ul>
                <p>The 422-acre Kayuta Lake averages 10 feet deep with a maximum depth of 22 feet. Key species found in the lake include panfish, bullhead, trout and smallmouth bass, pickerel, brown and lake trout. Be sure to bring your fishing gear! Don\'t have your own gear or forgot it at home, come to the General Store for Rods, hooks, bait, and tackle!</p>',
            'status' => 1,
            'created_at' => '2023-09-30 06:47:26',
            'updated_at' => '2023-11-05 14:46:10',
        ]);
    }
}
