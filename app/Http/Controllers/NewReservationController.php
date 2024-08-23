<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Reservation;
use App\Models\SiteClass;
use App\Models\SiteHookup;
use App\Models\Site;
use App\Models\Payment;
use Carbon\Carbon;
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
        // $payments = Payment::all();
        // $limit = $request->input('limit', 10);
        // $reservations = Reservation::orderBy('id', 'DESC')->paginate($limit);

        // return response()->json($reservations);

        $limit = $request->input('limit', 10);
        $paymentCartIds = Payment::pluck('cartid');
        
        $reservations = Reservation::whereIn('cartid', $paymentCartIds)
                                    ->orderBy('id', 'DESC')
                                    ->paginate($limit);
        
        return response()->json($reservations);
        
    }

    public function noCart(Request $request)
    {
        $limit = $request->input('limit', 10);
        $paymentCartIds = Payment::pluck('cartid');
        
        $reservations = Reservation::whereNotIn('cartid', $paymentCartIds)
                                    ->orderBy('id', 'DESC')
                                    ->paginate($limit);
        
        return response()->json($reservations);
    }

    public function getCustomers()
    {
        $customer = Customer::orderBy('id', 'DESC')->get();
        return response()->json($customer);
    }

    public function getSiteClasses()
    {
        $siteclass = SiteClass::all();
        return response()->json($siteclass);
    }

    public function getSiteHookups()
    {
        $hookup = SiteHookup::all();
        return response()->json($hookup);
    }

    public function getSites()
    {
        $currentDate = now();
        $reservedSiteIds = Reservation::where(function ($query) use ($currentDate   ) {
            $query->whereBetween('cid', [$currentDate, '9999-12-31'])
            ->orWhereBetween('cod', ['0000-01-01', $currentDate]);
        })->pluck('siteid')->toArray();

      $site = Site::whereNotIn('siteid', $reservedSiteIds)->get();
      return response()->json($site);

    }

    public function paymentIndex(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        return view('reservations.payment', compact('reservation'));
    }

    public function storeInfo(Request $request)
    {

        $randomId = rand(9999, 99999);


        $fromDate = Carbon::parse($request->fromDate);
        $toDate = Carbon::parse($request->toDate);
        $numberOfNights = $toDate->diffInDays($fromDate);
        $reservation = new Reservation();
        $reservation->cid = $request->fromDate;
        $reservation->cod = $request->toDate;
        $reservation->fname = $request->fname;
        $reservation->lname = $request->lname;
        $reservation->email = $request->email;
        $reservation->siteclass = $request->siteclass;
        $reservation->riglength = $request->riglength;
        $reservation->rigtype = $request->hookup;
        $reservation->nights = $numberOfNights;
        $reservation->pets = $request->pets;
        $reservation->adults = $request->adults;
        $reservation->children = $request->children;
        $reservation->cartid = $randomId;
        $reservation->siteid = $request->siteId;

        $reservation->receipt = 0;
        $reservation->total = 0;
        $reservation->subtotal = 0;
        $reservation->extracharge = 0;
        $reservation->base = 0;
        $reservation->rateadjustment = 0;
        $reservation->totaltax = 0;
        $reservation->totalcharges = 0;
        $reservation->totalpayments = 0;
        $reservation->balance = 0;
        $reservation->discount = 0;
        $reservation->xconfnum = 0;
        $reservation->save();

        return response()->json(['success' => true]);

        
       
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
