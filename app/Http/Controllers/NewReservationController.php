<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Reservation;
use App\Models\SiteClass;
use App\Models\SiteHookup;
use App\Models\RateTier;
use App\Models\Site;
use App\Models\Payment;
use App\Models\CartReservation;
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
        $limit = $request->input('limit', 10);
        $paymentCartIds = Payment::pluck('cartid')->toArray();
        $today = now()->toDateString();

        $reservations = Reservation::whereIn('cartid', $paymentCartIds)
            ->orderBy('id', 'DESC')
            ->paginate($limit);



        return response()->json($reservations);
    }

    
    
    


   

    public function noCart(Request $request)
    {
        $limit = $request->input('limit', 10);
        $paymentCartIds = Payment::pluck('cartid');
    
        $reservations = CartReservation::whereNotIn('cartid', $paymentCartIds)
                        ->leftJoin('customers', 'cart_reservations.customernumber', '=', 'customers.id')
                        ->select('cart_reservations.*', 'customers.first_name', 'customers.last_name')
                        ->orderBy('cart_reservations.id', 'DESC')
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
        $reservation = CartReservation::findOrFail($id);
        return view('reservations.payment', compact('reservation'));
    }
    
    

    public function storeInfo(Request $request)
    {
        $randomId = rand(9999, 99999);
    
        $fromDate = Carbon::parse($request->fromDate);
        $toDate = Carbon::parse($request->toDate);
        $numberOfNights = $toDate->diffInDays($fromDate);
    
        $rvSiteClasses = ['WE30A', 'WSE30A', 'WSE50A', 'WE50A', 'NOHU'];
        $tier = null;
    
        if ($request->siteclass === 'RV Sites') {
           
            if (in_array($request->hookup, $rvSiteClasses)) {
                $tier = RateTier::where('tier', $request->hookup)->first();
            } else if ($request->hookup === 'No Hookup') {
                $tier = RateTier::where('tier', 'NOHU')->first();
            } else {
                return response()->json(['error' => 'Invalid hookup type selected'], 400);
            }
        } else if ($request->siteclass === 'Boat Slips') {
            $tier = RateTier::where('tier', 'BOAT')->first();
        } else if($request->siteclass === 'Jet Ski Slips') {
            $tier = RateTier::where('tier', 'JETSKI')->first();
        } else {
            $tier = RateTier::where('tier', $request->siteclass)->first();
        }
    
    
        $rates = [
            'Sunday' => $tier->sundayrate,
            'Monday' => $tier->mondayrate,
            'Tuesday' => $tier->tuesdayrate,
            'Wednesday' => $tier->wednesdayrate,
            'Thursday' => $tier->thursdayrate,
            'Friday' => $tier->fridayrate,
            'Saturday' => $tier->saturdayrate
        ];
    
        $baseRate = 0;
        for ($i = 0; $i < $numberOfNights; $i++) {
            $day = $fromDate->copy()->addDays($i)->format('l');
            $baseRate += $rates[$day];
        }
        
        $siteLockValue = $request->siteLock === 'on' ? 20 : 0;
        $subtotal =($baseRate * $numberOfNights) + $siteLockValue;
        $taxRate = 8.7;
        $totalTax = ($subtotal * $taxRate) / 100;
        $total = $subtotal + $totalTax;
    
        $cart = new CartReservation();
        $cart->customernumber = $request->customernumber;
        $cart->cid = $fromDate;
        $cart->cod = $toDate;
        $cart->cartid = $randomId;
        $cart->siteid = $request->siteId;
        $cart->riglength = $request->riglength;
        $cart->sitelock = $siteLockValue;
        $cart->nights = $numberOfNights;
        $cart->siteclass = $request->siteclass;
        $cart->hookups = $request->hookup ?? 0;
        $cart->email = $request->email;
        $cart->base = $baseRate;
        $cart->subtotal = $subtotal;
        $cart->taxrate = $taxRate;
        $cart->totaltax = $totalTax;
        $cart->total = $total;
        $cart->rid = "uc";
        $cart->description = "{$numberOfNights} night(s) for {$cart->siteclass} at {$request->siteId}";
    
        $cart->save();
    
        return response()->json(['success' => true, 'total' => $total, 'subtotal' => $subtotal, 'tax' => $totalTax]);
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

    public function storePayment(Request $request, $id){
  
        $cart_reservation = CartReservation::findOrFail($id);
        $customer = Customer::where('id', $cart_reservation->customernumber)->first();

   
        $randomReceiptID = rand(0000, 0000010000);
        if($request->transactionType === 'Cash'){
            $savepayment = new Payment();
            $savepayment->cartid = $request->cartid;
            $savepayment->receipt = $randomReceiptID;
            $savepayment->method = $request->transactionType;
            $savepayment->customernumber = $cart_reservation->customernumber;
            $savepayment->email = $cart_reservation->email;
            $savepayment->payment = $cart_reservation->total;
            $savepayment->save();

            $savereservation = new Reservation();
            $savereservation->cartid = $request->cartid;
            $savereservation->source = 'Walk In';
            $savereservation->email = $cart_reservation->email;
            $savereservation->fname = $customer->first_name;
            $savereservation->lname = $customer->last_name;
            $savereservation->customernumber = $cart_reservation->customernumber;
            $savereservation->siteid = $cart_reservation->siteid;
            $savereservation->cid = $cart_reservation->cid;
            $savereservation->cod = $cart_reservation->cod;
            $savereservation->total = $cart_reservation->total;
            $savereservation->subtotal = $cart_reservation->subtotal;
            $savereservation->taxrate = $cart_reservation->taxrate;
            $savereservation->totaltax = $cart_reservation->totaltax;
            $savereservation->siteclass = $cart_reservation->siteclass;
            $savereservation->nights = $cart_reservation->nights;
            $savereservation->base = $cart_reservation->base;
            $savereservation->sitelock = $cart_reservation->sitelock;
            $savereservation->rigtype = $cart_reservation->hookups;
            $savereservation->riglength = $cart_reservation->riglength;
            $savereservation->xconfnum = 0;
            $savereservation->createdby = 'Admin';
            $savereservation->receipt = $savepayment->receipt;
            $savereservation->rateadjustment = 0;
            $savereservation->rid = 'uc';
            $savereservation->save();

            return response()->json(['success' => true]);
        } else {
         
            $apiKey = config('services.cardknox.api_key');
            $apiSecret = config('services.cardknox.api_secret');
            $cardNumber = $request->input('xCardNum');
            $xExp = str_replace('/', '', $request->xExp);
        
            $data = [
                'xKey' => $apiKey,
                'xVersion' => '4.5.5',
                'xCommand' => 'cc:Sale',
                'xAmount' => $cart_reservation->total,
                'xCardNum' => $cardNumber,
                'xExp' => $xExp,
                'xSoftwareVersion' => '1.0',
                'xSoftwareName' => 'KayutaLake'
            ];
        
            $ch = curl_init('https://x1.cardknox.com/gateway');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-type: application/x-www-form-urlencoded',
                'X-Recurring-Api-Version: 1.0',
            ]);
        
            
            $responseContent = curl_exec($ch);
            curl_close($ch);
        
            if ($responseContent === false) {
                return redirect()->back()->with('error', 'Error communicating with payment gateway.');
            }
        
            parse_str($responseContent, $responseArray);
        
            if ($responseArray['xStatus'] == 'Error') {
                return redirect()->back()->with('error', $responseArray['xError']);
            } else if ($responseArray['xStatus'] == 'Approved') {
                $xAuthCode = $responseArray['xAuthCode'];
                $xToken = $responseArray['xToken'];
        
             
                // $carts = CartReservation::where('id', $id)->get();

                
                $savepayment = new Payment();
                $savepayment->cartid = $request->cartid;
                $savepayment->receipt = $randomReceiptID;
                $savepayment->method = $request->transactionType;
                $savepayment->customernumber = $cart_reservation->customernumber;
                $savepayment->email = $cart_reservation->email;
                $savepayment->payment = $cart_reservation->total;
                $savepayment->save();

                $savereservation = new Reservation();
                $savereservation->cartid = $request->cartid;
                $savereservation->source = 'Walk In';
                $savereservation->email = $cart_reservation->email;
                $savereservation->fname = $customer->first_name;
                $savereservation->lname = $customer->last_name;
                $savereservation->customernumber = $cart_reservation->customernumber;
                $savereservation->siteid = $cart_reservation->siteid;
                $savereservation->cid = $cart_reservation->cid;
                $savereservation->cod = $cart_reservation->cod;
                $savereservation->total = $cart_reservation->total;
                $savereservation->subtotal = $cart_reservation->subtotal;
                $savereservation->taxrate = $cart_reservation->taxrate;
                $savereservation->totaltax = $cart_reservation->totaltax;
                $savereservation->siteclass = $cart_reservation->siteclass;
                $savereservation->nights = $cart_reservation->nights;
                $savereservation->base = $cart_reservation->base;
                $savereservation->sitelock = $cart_reservation->sitelock;
                $savereservation->rigtype = $cart_reservation->hookups;
                $savereservation->riglength = $cart_reservation->riglength;
                $savereservation->xconfnum = $xAuthCode;
                $savereservation->createdby = 'Admin';
                $savereservation->receipt = $savepayment->receipt;
                $savereservation->rateadjustment = 0;
                $savereservation->rid = 'uc';
                $savereservation->save();
    
        
                // $reservationIds = [];
                // foreach ($carts as $cart) {
                //     $receipt = $this->receipt->storeReceipt(['cartid' => $cart->cartid]);
        
              
                //     // $xMaskedCardNumber = $responseArray['xMaskedCardNumber'];
                //     // $this->cardsOnFile->storeCards([
                //     //     'customernumber' => $cart->customernumber,
                //     //     'method' => $responseArray['xCardType'],
                //     //     'cartid' => $cart->cartid,
                //     //     'email' => $cart->email,
                //     //     'xmaskedcardnumber' => $xMaskedCardNumber,
                //     //     'xtoken' => $xToken,
                //     //     'receipt' => $savepayment->receipt,
                //     //     'gateway_response' => json_encode($responseArray)
                //     // ]);
        
                   
        
                //     // $this->cartReservation->deleteById($cart->cartid);
                // }
        
                return response()->json(['success' => true, 'reservation_ids' => $reservationIds]);
            } else {
                return redirect()->back()->with('error', 'Unexpected response from payment gateway.');
            }
        }
        
    }
    

   

    
   
    
}
