<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Site;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Customer;
use App\Models\GiftCard;
use App\Models\RateTier;
use App\Models\SiteClass;
use App\Models\SiteHookup;
use App\Events\CartDeleted;
use App\Jobs\DeleteCartJob;
use App\Models\CardsOnFile;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\CartReservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class NewReservationController extends Controller
{
    public function index($id)
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

    public function updateCheckedIn(Request $request)
    {
        $cartid = $request->input('cartid');

        $request->validate([
            'cartid' => 'required|exists:reservations,cartid',
        ]);

        try{
            $reservation = Reservation::where('cartid', $cartid)->firstOrFail();
            $reservation->update([
                'checkedin' => Carbon::now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Reservation checked in successfully.', 'checked_in_date' => $reservation->checkedin->format('Y-m-d H:i:s')], 200);
        } catch (Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to checked-in status',
                'error' => $e->getMessage()
            ], 500);
        }

        
    }

    public function updateCheckedOut(Request $request)
    {
        $cartid = $request->input('cartid');
        $request->validate([
            'cartid' => 'required|exists:reservations,cartid',
        ]);

        try{
            $reservation = Reservation::where('cartid', $cartid)->firstOrFail();
            $reservation->update([
                'checkedout' => Carbon::now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reservation checked out successfully.',
                'checked_out_date' => $reservation->checkedout->format('Y-m-d H:i:s')
            ], 200);    
            
        } catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to checked-out status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function getReservations(Request $request)
    {
        $limit = $request->input('limit', 10);
        $siteId = $request->input('siteid');
        $types = $request->input('tier', []);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $paymentCartIds = Payment::pluck('cartid')->toArray();

        $reservationsQuery = Reservation::join('customers', 'reservations.customernumber', '=', 'customers.id')
            ->join('payments', 'reservations.cartid', '=', 'payments.cartid')
            ->whereIn('reservations.cartid', $paymentCartIds)
            ->select('reservations.*', 'customers.phone', 'customers.address', 'payments.payment')
            ->orderByDesc('reservations.id')
            ->distinct('reservations.id'); 
        
        if($siteId){
            $reservationsQuery->where('reservations.siteid', $siteId);
        }

        if(!empty($types)){
            $reservationsQuery->whereIn('reservations.siteclass', $types);
        }

        if($startDate && $endDate){
            $reservationsQuery->where(function ($query) use ($startDate, $endDate) {
                $query->whereDate('reservations.cid', '>=', $startDate)
                    ->whereDate('reservations.cod', '<=', $endDate);
            });
        }elseif ($startDate) {
            $reservationsQuery->whereDate('reservations.cid', '>=', $startDate);
        } elseif ($endDate) {
            $reservationsQuery->whereDate('reservations.cod', '<=', $endDate);
        }

        $reservations = $reservationsQuery->paginate($limit);

        return response()->json($reservations);
    }




    public function noCart(Request $request)
    {
        $limit = $request->input('limit', 10);
        $paymentCartIds = Payment::pluck('cartid');

        $reservations = CartReservation::whereNotIn('cartid', $paymentCartIds)
        ->leftJoin('customers', 'cart_reservations.customernumber', '=', 'customers.id')
        ->select('cart_reservations.*', 'customers.first_name', 'customers.last_name')
        ->orderBy('cart_reservations.id', 'DESC')->paginate($limit);

        
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

    public function getAddon()
    {
        $product = Product::where('suggested_addon', 1)->get();
        return response()->json($product);
    }




    public function getSiteHookups()
    {
        $hookup = SiteHookup::all();
        return response()->json($hookup);
    }

    public function getSites(Request $request)
    {
        $fromDate = Carbon::parse($request->fromDate);
        $toDate = Carbon::parse($request->toDate);
    
        $reservedSiteIds = Reservation::where('cid', '<', $toDate)
            ->where('cod', '>', $fromDate)
            ->pluck('siteid')
            ->toArray();
    
        $siteQuery = Site::whereNotIn('siteid', $reservedSiteIds);
    
        if ($request->has('siteclass') && !empty($request->siteclass)) {
            $siteclassArray = explode(',', trim($request->siteclass));
            $siteclasses = array_map(function($value){
                return str_replace(' ', '_', trim($value));
            }, $siteclassArray);
    
            if (!empty($siteclasses)) {
                $siteQuery->where(function($query) use ($siteclasses, $request) {
                    if (in_array('RV_Sites', $siteclasses)) {
                        $query->where(function($q) use ($request) {
                            $q->where('siteclass', 'RV_Sites')
                              ->orWhere('siteclass', 'RV_Sites,Tent_Sites');
                            if ($request->has('hookup') && !empty($request->hookup)) {
                                $hookup = $request->hookup;
                                $q->where('hookup', $hookup);
                            }
                        });
    
                        $siteclasses = array_diff($siteclasses, ['RV_Sites']);
                    }
    
                    if (in_array('Tent_Sites', $siteclasses)) {
                        $query->orWhere(function($q) {
                            $q->where('siteclass', 'Tent_Sites')
                              ->orWhere('siteclass', 'RV_Sites,Tent_Sites');
                        });
    
                       
                        $siteclasses = array_diff($siteclasses, ['Tent_Sites']);
                    }
    
                    if (!empty($siteclasses)) {
                        $query->orWhereIn('siteclass', $siteclasses);
                    }
                });
            }
        }
    
        $sites = $siteQuery->get();
    
        return response()->json($sites);
    }
    
    


    public function paymentIndex(Request $request, $id)
    {
        $reservation = CartReservation::findOrFail($id);
        return view('reservations.payment', compact('reservation'));
    }

    public function invoice(Request $request, $cartid)
    {
        $payment = Payment::where('cartid', $cartid)->firstOrFail();
        $cart = CartReservation::where('cartid', $cartid)->firstOrFail();
        $reservation = Reservation::where('cartid', $payment->cartid)->firstOrFail();

        return view('reservations.payment', compact('payment', 'reservation', 'cart'));
    }

    public function storeInfo(Request $request)
    {
        $randomId = substr(bin2hex(random_bytes(7)), 0, 13);
        $fromDate = Carbon::parse($request->fromDate);
        $toDate = Carbon::parse($request->toDate);
        $currentUTC = now('UTC');
        
        
        // $rvSiteClasses = ['WE30A', 'WSE30A', 'WSE50A', 'WE50A', 'NOHU'];
        $site = Site::where('siteid', $request->siteId)->first();
        $customer = Customer::firstOrCreate(
            ['email' => $request->email],
            [
                'first_name' => $request->f_name,
                'last_name' => $request->l_name,
                'phone' => $request->con_num,
                'address' => $request->address,
                'user_id' => 0
            ]
        );
    
        if (!$site) {
            return response()->json(['error' => 'Site not found'], 404);
        }
        $cartReservation = new CartReservation();

        $tier = $cartReservation->getTierForSiteClass($request);
    
        if (!$tier) {
            return response()->json(['error' => 'Rate tier not found'], 400);
        }
    
        try {
            $calculation = $cartReservation->calculateRate($fromDate, $toDate, $tier, $request);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    
        $cart = CartReservation::create([
            'customernumber' => $customer->id,
            'cid' => $fromDate,
            'cod' => $toDate,
            'cartid' => $randomId,
            'siteid' => $request->siteId,
            'riglength' => $request->riglength,
            'sitelock' => $request->siteLock === 'on' ? 20 : 0,
            'nights' => $calculation['numberOfNights'],
            'siteclass' => $request->siteclass,
            'hookups' => $request->hookup ?? 0,
            'email' => $request->email,
            'base' => $calculation['base_rate'],
            'subtotal' => $calculation['subtotal'],
            'number_of_guests' => $request->num_guests ?? 0,
            'taxrate' => 0.0875,
            'totaltax' => $calculation['totalTax'],
            'total' => $calculation['total'],
            'rid' => 'uc',
            'holduntil' => $currentUTC,
            'description' => "{$calculation['numberOfNights']} night(s) for {$request->siteclass} at {$request->siteId}",
        ]);
    
        return response()->json(['success' => true, 'total' => $calculation['total'], 'subtotal' => $calculation['subtotal'], 'tax' => $calculation['totalTax'], 'id' => $cart->id]);
    }
    
   

    public function store(Request $request)
    {
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


  


    public function processPayment(Request $request, $id)
    {
        $request->merge(['xAmount' => str_replace(',', '', $request->xAmount)]);

        $request->validate([
            'xAmount' => 'required|numeric',
        ]);
    
        $invoiceRandom = random_int(100000, 999999);
        $cart_reservation = CartReservation::findOrFail($id);
        $customer = Customer::find($cart_reservation->customernumber);
        $randomReceiptID = rand(1000, 9999);
        $paymentType = $request->paymentType;
    
        $payment = Payment::create([
            'cartid' => $request->cartid,
            'receipt' => $randomReceiptID,
            'method' => $paymentType,
            'customernumber' => $cart_reservation->customernumber,
            'email' => $cart_reservation->email,
            'payment' => number_format(floatval($request->xAmount), 2, '.', ''),
        ]);
    
        $this->saveReservation($cart_reservation, $customer, $randomReceiptID, $request);
    
        return response()->json([
            'success' => true,
            'message' => 'Payment processed and reservation saved successfully.',
        ]);
    }
    
    
    
    public function storePayment(Request $request, $id)
    {
        $cart_reservation = CartReservation::findOrFail($id);
        $customer = Customer::find($cart_reservation->customernumber);
        $randomReceiptID = rand(1000, 9999);
        $apiKey = config('services.cardknox.api_key');
        $paymentType = $request->paymentType;
        $amount = $request->input('xAmount');
        $uniqueTransactionId = uniqid('token_', true);
        $data['xTID'] = $uniqueTransactionId;

        $data = [
            'xKey' => $apiKey,
            'xVersion' => '4.5.5',
            'xAmount' => $amount,
            'xSoftwareVersion' => '1.0',
            'xSoftwareName' => 'KayutaLake',
            'xAllowDuplicate' => true,
         
        ];

        switch ($paymentType) {
            case 'Cash':
            case 'Other':
                $this->handleCashOrOtherPayment($cart_reservation, $customer, $request, $randomReceiptID, $paymentType);
                break;

            case 'Check':
                $data['xCommand'] = 'check:sale';
                $data['xAccount'] = $request->input('xAccount');
                $data['xRouting'] = $request->input('xRouting');
                $data['xName'] = $request->input('xName');
                return $this->handleCardknoxPayment($data, $cart_reservation, $customer, $request, $randomReceiptID, $paymentType, $uniqueTransactionId);

            case 'Manual':
                $data['xCommand'] = 'cc:sale';
                $data['xCardNum'] = $request->input('xCardNum');
                $data['xExp'] = str_replace('/', '', $request->input('xExp'));
                return $this->handleCardknoxPayment($data, $cart_reservation, $customer, $request, $randomReceiptID, $paymentType, $uniqueTransactionId);

            case 'Gift Card':
                $amount = $request->input('xAmount');
                $barcode = $request->input('xBarcode');
                $this->handleGiftCardPayment($cart_reservation, $customer, $request, $randomReceiptID, $paymentType, $barcode, $amount);
                break;

            default:
                return response()->json(['message' => 'Invalid payment type'], 400);
        }

        return response()->json(['success' => true]);
    }

    private function handleGiftCardPayment($cart_reservation, $customer, $request, $randomReceiptID, $paymentType, $barcode, $amount)
    {


        $giftcard = GiftCard::where('barcode', $barcode)->firstOrFail();
        $giftcard->amount -= $amount;
        $giftcard->save();

        $payment = new Payment([
            'cartid' => $request->cartid,
            'receipt' => $randomReceiptID,
            'method' => $paymentType,
            'customernumber' => $cart_reservation->customernumber,
           
            'email' => $cart_reservation->email,
            'payment' => $amount,
        ]);

        $payment->save();

        $this->saveReservation($cart_reservation, $customer, $randomReceiptID, $request);

    }

    private function handleCashOrOtherPayment($cart_reservation, $customer, $request, $randomReceiptID, $paymentType)
    {
        $payment = new Payment([
            'cartid' => $request->cartid,
            'receipt' => $randomReceiptID,
            'method' => $paymentType,
            'customernumber' => $cart_reservation->customernumber,
            'email' => $cart_reservation->email,
            'payment' => $request->xCash,
        ]);
        $payment->save();

        $this->saveReservation($cart_reservation, $customer, $randomReceiptID, $request);
    }

    private function handleCardknoxPayment($data, $cart_reservation, $customer, $request, $randomReceiptID, $paymentType, $uniqueTransactionId)
    {
        $ch = curl_init('https://x1.cardknox.com/gateway');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/x-www-form-urlencoded', 'X-Recurring-Api-Version: 1.0']);

        $responseContent = curl_exec($ch);


        curl_close($ch);

        if ($responseContent === false) {
            return response()->json(['message' => 'Error communicating with payment gateway.'], 500);
        }

        parse_str($responseContent, $responseArray);

        if (isset($responseArray['xStatus']) && $responseArray['xStatus'] === 'Approved') {
            $payment = new Payment([
                'cartid' => $request->cartid,
                'receipt' => $randomReceiptID,
                'method' => $paymentType,
                'customernumber' => $cart_reservation->customernumber,
                'email' => $cart_reservation->email,
                'payment' => $request->xAmount,
            ]);
            $payment->save();

            $this->saveReservation($cart_reservation, $customer, $randomReceiptID, $request);
            if($paymentType === 'Manual'){
                $this->saveCardonFiles($cart_reservation, $customer, $randomReceiptID, $request, $uniqueTransactionId);

            }
            return response()->json(['success' => true]);
        } else {
            return response()->json(['message' => 'Payment failed: ' . ($responseArray['xError'] ?? 'Unknown error')], 400);
        }
    }

    private function saveReservation($cart_reservation, $customer, $randomReceiptID, $request)
    {
        $reservation = new Reservation([
            'cartid' => $request->cartid,
            'source' => 'Reservation',
            'email' => $cart_reservation->email,
            'fname' => $customer->first_name,
            'lname' => $customer->last_name,
            'customernumber' => $cart_reservation->customernumber,
            'siteid' => $cart_reservation->siteid,
            'cid' => $cart_reservation->cid,
            'cod' => $cart_reservation->cod,
            'total' => $cart_reservation->total,
            'subtotal' => $cart_reservation->subtotal,
            'taxrate' => $cart_reservation->taxrate,
            'totaltax' => $cart_reservation->totaltax,
            'siteclass' => $cart_reservation->siteclass,
            'nights' => $cart_reservation->nights,
            'base' => $cart_reservation->base,
            'sitelock' => $cart_reservation->sitelock,
            'rigtype' => $cart_reservation->hookups,
            'riglength' => $cart_reservation->riglength,
            'xconfnum' => $request->xconfnum ?? '123',
            'createdby' => auth()->user()->name, 
            'receipt' => $randomReceiptID,
            'rateadjustment' => 0,
            'rid' => 'uc',
            
            
        ]);
        $reservation->save();
            



     
    }

    private function saveCardonFiles($cart_reservation, $customer, $randomReceiptID, $request, $uniqueTransactionId)
    {
        $fullCardNumber = $request->xCardNum;
        $maskedCardNumber = substr($fullCardNumber, 0, 1) . str_repeat('*', strlen($fullCardNumber) - 1) . substr($fullCardNumber, -3);
        $cardType = $this->getCardType($fullCardNumber);


        Log::info('Saving card on file', [
            'customernumber' => $customer->id,
            'cartid' => $cart_reservation->cartid,
            'method' => $cardType,
            'receipt' => $randomReceiptID,
            'email' => $customer->email,
            'maskedCardNumber' => $maskedCardNumber,
            'xToken' => $uniqueTransactionId,
            'gateway_response' => $request->gateway_response,
        ]);

        try {
            $cardonFiles = new CardsOnFile([
                'customernumber' => $customer->id,
                'cartid' => $cart_reservation->cartid,
                'method' => $cardType,
                'receipt' => $randomReceiptID,
                'email' => $customer->email,
                'xmaskedcardnumber' => $maskedCardNumber,
                'xToken' => $uniqueTransactionId,
                'gateway_response' => json_encode($request->gateway_response),
            ]);

            $cardonFiles->save();
        } catch (\Exception $e) {
            Log::error('Error saving card on file', ['exception' => $e->getMessage()]);

        }
    }


    /**
     * Determine the card type based on the card number.
     *
     * @param string $cardNumber
     * @return string
     */

    private function getCardType($cardNumber)
    {
        $cardType = 'Unkown';
        if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $cardNumber)) {
            $cardType = 'Visa';
        } elseif (preg_match('/^5[1-5][0-9]{14}$/', $cardNumber)) {
            $cardType = 'MasterCard';
        } elseif (preg_match('/^3[47][0-9]{13}$/', $cardNumber)) {
            $cardType = 'American Express';
        } elseif (preg_match('/^6(?:011|5[0-9]{2})[0-9]{12}$/', $cardNumber)) {
            $cardType = 'Discover';
        } elseif (preg_match('/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/', $cardNumber)) {
            $cardType = 'Diners Club';
        } elseif (preg_match('/^35[0-9]{14}$/', $cardNumber)) {
            $cardType = 'JCB';
        }

        return $cardType;
    }




    public function deleteCart(Request $request)
    {
       
        $currentUTC = now('UTC');
    
        $timeThreshold = $currentUTC->subMinutes(30);
    
        $carts = CartReservation::where('created_at', '<=', $timeThreshold)->get();
    
        foreach ($carts as $cart) {
            $cartid = $cart->cartid;
    
            CartReservation::where('cartid', $cartid)->delete();
    
            broadcast(new CartDeleted($cartid));
    
            Log::info("Cart {$cartid} has been deleted.");
        }
    
        return response()->json(['success' => true, 'message' => 'All expired carts have been deleted.']);
    }
    

    
   
    
}
