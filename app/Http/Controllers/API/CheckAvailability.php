<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Site;
use App\Models\CartReservation;
use App\Models\Reservation;
use App\Models\API\SitesAvailable;
use App\Models\RateTier;
class CheckAvailability extends Controller
{   


    public function getSites(Request $request)
    {
        $dateNow = $request->input('date') ?? Carbon::now()->toDateString(); 
    
        $availableSites = SitesAvailable::getAvailableSites($dateNow);
        $rate_tier = RateTier::all();
    
        return response()->json([
            'success' => true,
            'sites' => $availableSites,
            'rate_tier' => $rate_tier
        ]);
    }
    

    public function getReservAndSites()
    {
        $sites = Site::all();
        $reservations = Reservation::all();

        return response()->json([
            'sites' => $sites,
            'reservations' => $reservations
        ]);
    }


  
    
}
