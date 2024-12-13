<?php

namespace App\Http\Controllers\API;

use App\Models\RateTier;
use App\Models\Reservation;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DataController extends Controller
{
    public function getData()
    {
        $data = [
            'sites' => Site::all(),
            'rate_tier' => RateTier::all(),
            'reservations' => Reservation::all()
        ];

        return response()->json(['data' => $data]);
    }
}
