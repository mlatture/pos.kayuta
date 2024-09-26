<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;

class CalendarReservationController extends Controller
{
    public function index($id)
    {
        return view('reservations.calendar', compact('id'));
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
