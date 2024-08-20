<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
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

    public function getReservations(Request $request)
    {
        $limit = $request->input('limit', 10);
        $reservations = Reservation::orderBy('id', 'DESC')->paginate($limit);

        return response()->json($reservations);

       
    }

    public function getCustomers()
    {
        $customer = Customer::all();
        return response()->json($customer);
    }

    public function store(Request $request){
        $customer = new Customer();
        $customer->first_name = $request->fname;
        $customer->last_name = $request->lname;
        $customer->email = $request->email;
        $customer->phone = $request->contactno;
        $customer->address = $request->address;
        $customer->user_id = 0;
        $customer->save();

        return response()->json(['success' => true]);
    }
    

    
   
    
}
