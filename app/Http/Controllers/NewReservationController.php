<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;

class NewReservationController extends Controller
{
    public function index()
    {
        return view('reservations.index');
    }

    public function updateReservation(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->cid = $request->input('start_date');
        $reservation->cod = $request->input('end_date');
        $reservation->save();

        return response()->json(['success' => true]);
    }

    public function getReservations()
    {
        $reservations = Reservation::all();
        $events = $reservations->map(function ($reservation) {
            return [
                'id' => $reservation->id,
                'title' => $reservation->fname . ' ' . $reservation->lname,
                'start' => $reservation->cid->toIso8601String(),
                'end' => $reservation->cod->toIso8601String(),
                'siteclass' => $reservation->siteclass // Include siteclass
            ];
        });
    
        return response()->json([
            'events' => $events
        ]);
    }
    

    
   
    
}
