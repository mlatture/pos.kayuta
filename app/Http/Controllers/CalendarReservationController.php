<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\SiteClass;

class CalendarReservationController extends Controller
{
    public function index($id)
    {   
        $reservation = Reservation::where('cartid', $id)->first();
        $getSiteClass = SiteClass::all();
        return view('reservations.relocate', ['reservation' => $reservation, 'siteclasses' => $getSiteClass]);
    }

    public function getUnavailableDates($id)
    {
        $reservations = Reservation::get(['cid', 'cod']);

        $unavailable_dates = [];
        foreach ($reservations as $reservation) {
            $unavailable_dates[] = [
                'cid' => date('Y-m-d', strtotime($reservation->cid)),
                'cod' => date('Y-m-d', strtotime($reservation->cod))
            ];
        }

        return response()->json([
            'unavailable_dates' => $unavailable_dates
        ]);
    }
}
