<?php

namespace Database\Seeders;

use App\Models\UpsellRate;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UpsellRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UpsellRate::create(['rate_percent' => 50.00]);
    }
}
