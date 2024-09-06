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
use Illuminate\Support\Facades\Log;

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
        $siteId = $request->input('siteId');
        $type = $request->input('tier');
        $paymentCartIds = Payment::pluck('cartid')->toArray();

        $reservationsQuery = Reservation::join('customers', 'reservations.customernumber', '=', 'customers.id')
            ->whereIn('reservations.cartid', $paymentCartIds)
            ->select('reservations.*', 'customers.phone', 'customers.address')
            ->orderBy('reservations.id', 'DESC');

        if (!empty($siteId)) {
            $reservationsQuery->where('siteid', $siteId);
        }

        if (!empty($type)) {
            $reservationsQuery->whereIn('siteclass', $type);
        }

        $reservations = $reservationsQuery->paginate($limit);

        return response()->json($reservations);
    }



    public function noCart(Request $request)
    {
        $limit = $request->input('limit', 10);
        $paymentCartIds = Payment::pluck('cartid');

        $reservations = CartReservation::whereNotIn('cartid', $paymentCartIds)->leftJoin('customers', 'cart_reservations.customernumber', '=', 'customers.id')->select('cart_reservations.*', 'customers.first_name', 'customers.last_name')->orderBy('cart_reservations.id', 'DESC')->paginate($limit);

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
        $reservedSiteIds = Reservation::where(function ($query) use ($currentDate) {
            $query->whereBetween('cid', [$currentDate, '9999-12-31'])->orWhereBetween('cod', ['0000-01-01', $currentDate]);
        })
            ->pluck('siteid')
            ->toArray();

        $site = Site::whereNotIn('siteid', $reservedSiteIds)->get();
        return response()->json($site);
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
        $randomId = rand(9999, 99999);

        $fromDate = Carbon::parse($request->fromDate);
        $toDate = Carbon::parse($request->toDate);
        $numberOfNights = $toDate->diffInDays($fromDate);

        $currentDate = Carbon::now()->format('l');
        $rvSiteClasses = ['WE30A', 'WSE30A', 'WSE50A', 'WE50A', 'NOHU'];
        $site = Site::where('siteid', $request->siteId)->first();

        if (!$site) {
            return response()->json(['error' => 'Site not found'], 404);
        }
        $tier = null;
        if ($request->siteclass === 'RV Sites') {
            if (in_array($request->hookup, $rvSiteClasses)) {
                $tier = RateTier::where('tier', $request->hookup)->first();
            } elseif ($request->hookup === 'No Hookup') {
                $tier = RateTier::where('tier', 'NOHU')->first();
            } else {
                return response()->json(['error' => 'Invalid hookup type selected'], 400);
            }
        } elseif ($request->siteclass === 'Boat Slips') {
            $tier = RateTier::where('tier', 'BOAT')->first();
        } elseif ($request->siteclass === 'Jet Ski Slips') {
            $tier = RateTier::where('tier', 'JETSKI')->first();
        } else {
            $tier = RateTier::where('tier', $request->siteclass)->first();
        }

        if (!$tier) {
            return response()->json(['error' => 'Rate tier not found'], 400);
        }

        if ($numberOfNights === 30) {
            $rate = $tier->monthlyrate;
        } elseif ($numberOfNights === 7) {
            $rate = $tier->weeklyrate;
        } elseif ($numberOfNights === 1) {
            $rates = [
                'Sunday' => $tier->sundayrate,
                'Monday' => $tier->mondayrate,
                'Tuesday' => $tier->tuesdayrate,
                'Wednesday' => $tier->wednesdayrate,
                'Thursday' => $tier->thursdayrate,
                'Friday' => $tier->fridayrate,
                'Saturday' => $tier->saturdayrate,
            ];
            $baseRate = 0;
            for ($i = 0; $i < $numberOfNights; $i++) {
                $day = $fromDate->copy()->addDays($i)->format('l');
                $baseRate += $rates[$day];
            }

            $rate = $baseRate;
        } else {
            return response()->json(['error' => 'Invalid number of nights'], 400);
        }

        $siteLockValue = $request->siteLock === 'on' ? 20 : 0;
        $subtotal = $rate + $siteLockValue;
        $taxRate = 0.0875;
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
        $cart->base = 0;
        $cart->subtotal = $subtotal;
        $cart->taxrate = $taxRate;
        $cart->totaltax = $totalTax;
        $cart->total = $total;
        $cart->rid = 'uc';
        $cart->description = "{$numberOfNights} night(s) for {$cart->siteclass} at {$request->siteId}";

        $cart->save();

        return response()->json(['success' => true, 'total' => $total, 'subtotal' => $subtotal, 'tax' => $totalTax]);
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

    //     public function storePayment(Request $request, $id)
//     {
//         $cart_reservation = CartReservation::findOrFail($id);
//         $customer = Customer::where('id', $cart_reservation->customernumber)->first();
//         $randomReceiptID = rand(0000, 10000);

    //         $token = $request->input('xToken');
//         $amount = $request->input('xAmount');
//         $apiKey = config('services.cardknox.api_key');
//         $cardNumber = $request->input('xCardNum');
//         $xExp = str_replace('/', '', $request->xExp);

    //         $data = [
//             'xKey' => $apiKey,
//             'xVersion' => '4.5.5',
//             'xCommand' => 'cc:Sale',
//             'xAmount' => $amount,
//             'xCardNum' => $cardNumber,
//             'xExp' => $xExp,
//             'xSoftwareVersion' => '1.0',
//             'xSoftwareName' => 'KayutaLake',
//         ];

    //         $data1 = [
//             'xKey' => $apiKey,
//             'xVersion' => '4.5.5',
//             'xCommand' => 'check:Sale',
//             'xAmount' => $amount,
//             'xAccount' => $cardNumber,
//             'xSoftwareVersion' => '1.0',
//             'xSoftwareName' => 'KayutaLake',
//         ];

    //         $data2 = [
//             'xKey' => $apiKey,
//             'xVersion' => '4.5.5',
//             'xCommand' => 'cc:Sale',
//             'xAmount' => $amount,
//             'xSoftwareVersion' => '1.0',
//             'xSoftwareName' => 'KayutaLake',
//         ];

    //         $paymentType = $request->paymentType;

    //         switch ($paymentType) {
//             case 'Cash':
//             case 'Other':
//                 // Handle Cash payment and Other payment
//                 $savepayment = new Payment();
//                 $savepayment->cartid = $request->cartid;
//                 $savepayment->receipt = $randomReceiptID;
//                 $savepayment->method = $paymentType;
//                 $savepayment->customernumber = $cart_reservation->customernumber;
//                 $savepayment->description = $request->description ?? '';
//                 $savepayment->checknumber = $request->xCheckNum ?? '';
//                 $savepayment->email = $cart_reservation->email;
//                 $savepayment->payment = $request->xCash;
//                 $savepayment->save();

    //                 $savereservation = new Reservation();
//                 $savereservation->fill([
//                     'cartid' => $request->cartid,
//                     'source' => 'Walk In',
//                     'email' => $cart_reservation->email,
//                     'fname' => $customer->first_name,
//                     'lname' => $customer->last_name,
//                     'customernumber' => $cart_reservation->customernumber,
//                     'siteid' => $cart_reservation->siteid,
//                     'cid' => $cart_reservation->cid,
//                     'cod' => $cart_reservation->cod,
//                     'total' => $cart_reservation->total,
//                     'subtotal' => $cart_reservation->subtotal,
//                     'taxrate' => $cart_reservation->taxrate,
//                     'totaltax' => $cart_reservation->totaltax,
//                     'siteclass' => $cart_reservation->siteclass,
//                     'nights' => $cart_reservation->nights,
//                     'base' => $cart_reservation->base,
//                     'sitelock' => $cart_reservation->sitelock,
//                     'rigtype' => $cart_reservation->hookups,
//                     'riglength' => $cart_reservation->riglength,
//                     'xconfnum' => 0,
//                     'createdby' => 'Admin',
//                     'receipt' => $savepayment->receipt,
//                     'rateadjustment' => 0,
//                     'rid' => 'uc',
//                 ]);
//                 $savereservation->save();

    //                 return response()->json(['success' => true]);

    //             case 'Check':
//                 $ch = curl_init('https://x1.cardknox.com/gateway');
//                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//                 curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data1));
//                 curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/x-www-form-urlencoded', 'X-Recurring-Api-Version: 1.0']);

    //                 $responseContent = curl_exec($ch);
//                 curl_close($ch);

    //                 if ($responseContent === false) {
//                     return response()->json(['message' => 'Error communicating with payment gateway.'], 500);
//                 }

    //                 $responseArray = json_decode($responseContent, true);

    //                 if (isset($responseArray['xResult']) && $responseArray['xResult'] === '00') {
//                     $savepayment = new Payment();
//                     $savepayment->cartid = $request->cartid;
//                     $savepayment->receipt = $randomReceiptID;
//                     $savepayment->method = $paymentType;
//                     $savepayment->customernumber = $cart_reservation->customernumber;
//                     $savepayment->description = $request->description ?? '';
//                     $savepayment->checknumber = $request->xCheckNum ?? '';
//                     $savepayment->email = $cart_reservation->email;
//                     $savepayment->payment = $cart_reservation->total ?? '';
//                     $savepayment->save();

    //                     $savereservation = new Reservation();
//                     $savereservation->fill([
//                         'cartid' => $request->cartid,
//                         'source' => 'Walk In',
//                         'email' => $cart_reservation->email,
//                         'fname' => $customer->first_name,
//                         'lname' => $customer->last_name,
//                         'customernumber' => $cart_reservation->customernumber,
//                         'siteid' => $cart_reservation->siteid,
//                         'cid' => $cart_reservation->cid,
//                         'cod' => $cart_reservation->cod,
//                         'total' => $cart_reservation->total,
//                         'subtotal' => $cart_reservation->subtotal,
//                         'taxrate' => $cart_reservation->taxrate,
//                         'totaltax' => $cart_reservation->totaltax,
//                         'siteclass' => $cart_reservation->siteclass,
//                         'nights' => $cart_reservation->nights,
//                         'base' => $cart_reservation->base,
//                         'sitelock' => $cart_reservation->sitelock,
//                         'rigtype' => $cart_reservation->hookups,
//                         'riglength' => $cart_reservation->riglength,
//                         'xconfnum' => 0,
//                         'createdby' => 'Admin',
//                         'receipt' => $savepayment->receipt,
//                         'rateadjustment' => 0,
//                         'rid' => 'uc',
//                     ]);
//                     $savereservation->save();

    //                     return response()->json(['success' => true]);

    //                 } else {
//                     return response()->json(
//                         [
//                             'message' => 'Payment failed: ' . ($responseArray['xError'] ?? 'Unknown error'),
//                         ],
//                         400,
//                     );
//                 }

    //             case 'Manual':
//                 $ch = curl_init('https://x1.cardknox.com/gateway');
//                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//                 curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
//                 curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/x-www-form-urlencoded', 'X-Recurring-Api-Version: 1.0']);

    //                 $responseContent = curl_exec($ch);
//                 curl_close($ch);

    //                 if ($responseContent === false) {
//                     return response()->json(['message' => 'Error communicating with payment gateway.'], 500);
//                 }

    //                 parse_str($responseContent, $responseArray);

    //                 if (isset($responseArray['xStatus']) && $responseArray['xStatus'] === 'Approved') {
//                     $xAuthCode = $responseArray['xAuthCode'] ?? '';
//                     $xToken = $responseArray['xToken'] ?? '';

    //                     $savepayment = new Payment();
//                     $savepayment->cartid = $request->cartid;
//                     $savepayment->receipt = $randomReceiptID;
//                     $savepayment->method = $paymentType;
//                     $savepayment->customernumber = $cart_reservation->customernumber;
//                     $savepayment->email = $cart_reservation->email;
//                     $savepayment->payment = $cart_reservation->total;
//                     $savepayment->save();

    //                     $savereservation = new Reservation();
//                     $savereservation->fill([
//                         'cartid' => $request->cartid,
//                         'source' => 'Walk In',
//                         'email' => $cart_reservation->email,
//                         'fname' => $customer->first_name,
//                         'lname' => $customer->last_name,
//                         'customernumber' => $cart_reservation->customernumber,
//                         'siteid' => $cart_reservation->siteid,
//                         'cid' => $cart_reservation->cid,
//                         'cod' => $cart_reservation->cod,
//                         'total' => $cart_reservation->total,
//                         'subtotal' => $cart_reservation->subtotal,
//                         'taxrate' => $cart_reservation->taxrate,
//                         'totaltax' => $cart_reservation->totaltax,
//                         'siteclass' => $cart_reservation->siteclass,
//                         'nights' => $cart_reservation->nights,
//                         'base' => $cart_reservation->base,
//                         'sitelock' => $cart_reservation->sitelock,
//                         'rigtype' => $cart_reservation->hookups,
//                         'riglength' => $cart_reservation->riglength,
//                         'xconfnum' => 0,
//                         'createdby' => 'Admin',
//                         'receipt' => $savepayment->receipt,
//                         'rateadjustment' => 0,
//                         'rid' => 'uc',
//                     ]);
//                     $savereservation->save();

    //                     return response()->json(['success' => true]);
//                 } else {
//                     return response()->json(
//                         [
//                             'message' => 'Payment failed: ' . ($responseArray['xError'] ?? 'Unknown error'),
//                         ],
//                         400,
//                     );
//                 }

    //             case 'Terminal':
//                 $ch = curl_init('https://x1.cardknox.com/gateway');
//                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//                 curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
//                 curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/x-www-form-urlencoded', 'X-Recurring-Api-Version: 1.0']);


    //                 $responseContent = curl_exec($ch);
//                 curl_close($ch);

    //                 if ($responseContent === false) {
//                     return response()->json(['message' => 'Error communicating with payment gateway.'], 500);
//                 }

    //                 parse_str($responseContent, $responseArray);

    //                 if (isset($responseArray['xStatus']) && $responseArray['xStatus'] === 'Approved') {
//                     $xAuthCode = $responseArray['xAuthCode'] ?? '';
//                     $xToken = $responseArray['xToken'] ?? '';

    //                     $savepayment = new Payment();
//                     $savepayment->cartid = $request->cartid;
//                     $savepayment->receipt = $randomReceiptID;
//                     $savepayment->method = $paymentType;
//                     $savepayment->customernumber = $cart_reservation->customernumber;
//                     $savepayment->email = $cart_reservation->email;
//                     $savepayment->payment = $cart_reservation->total;
//                     $savepayment->save();

    //                     $savereservation = new Reservation();
//                     $savereservation->fill([
//                         'cartid' => $request->cartid,
//                         'source' => 'Walk In',
//                         'email' => $cart_reservation->email,
//                         'fname' => $customer->first_name,
//                         'lname' => $customer->last_name,
//                         'customernumber' => $cart_reservation->customernumber,
//                         'siteid' => $cart_reservation->siteid,
//                         'cid' => $cart_reservation->cid,
//                         'cod' => $cart_reservation->cod,
//                         'total' => $cart_reservation->total,
//                         'subtotal' => $cart_reservation->subtotal,
//                         'taxrate' => $cart_reservation->taxrate,
//                         'totaltax' => $cart_reservation->totaltax,
//                         'siteclass' => $cart_reservation->siteclass,
//                         'nights' => $cart_reservation->nights,
//                         'base' => $cart_reservation->base,
//                         'sitelock' => $cart_reservation->sitelock,
//                         'rigtype' => $cart_reservation->hookups,
//                         'riglength' => $cart_reservation->riglength,
//                         'xconfnum' => 0,
//                         'createdby' => 'Admin',
//                         'receipt' => $savepayment->receipt,
//                         'rateadjustment' => 0,
//                         'rid' => 'uc',
//                     ]);
//                     $savereservation->save();

    //                     return response()->json(['success' => true]);
//                 } else {
//                     return response()->json(
//                         [
//                             'message' => 'Payment failed: ' . ($responseArray['xError'] ?? 'Unknown error'),
//                         ],
//                         400,
//                     );
//                 }
//             case 'Gift Card':
//             {
//                 $savepayment = new Payment();
//                 $savepayment->cartid = $request->cartid;
//                 $savepayment->receipt = $randomReceiptID;
//                 $savepayment->method = $paymentType;
//                 $savepayment->customernumber = $cart_reservation->customernumber;
//                 $savepayment->description = $request->description ?? '';
//                 $savepayment->checknumber = $request->xCheckNum ?? '';
//                 $savepayment->email = $cart_reservation->email;
//                 $savepayment->payment = $request->xCash;
//                 $savepayment->save();

    //                 $savereservation = new Reservation();
//                 $savereservation->fill([
//                     'cartid' => $request->cartid,
//                     'source' => 'Walk In',
//                     'email' => $cart_reservation->email,
//                     'fname' => $customer->first_name,
//                     'lname' => $customer->last_name,
//                     'customernumber' => $cart_reservation->customernumber,
//                     'siteid' => $cart_reservation->siteid,
//                     'cid' => $cart_reservation->cid,
//                     'cod' => $cart_reservation->cod,
//                     'total' => $cart_reservation->total,
//                     'subtotal' => $cart_reservation->subtotal,
//                     'taxrate' => $cart_reservation->taxrate,
//                     'totaltax' => $cart_reservation->totaltax,
//                     'siteclass' => $cart_reservation->siteclass,
//                     'nights' => $cart_reservation->nights,
//                     'base' => $cart_reservation->base,
//                     'sitelock' => $cart_reservation->sitelock,
//                     'rigtype' => $cart_reservation->hookups,
//                     'riglength' => $cart_reservation->riglength,
//                     'xconfnum' => 0,
//                     'createdby' => 'Admin',
//                     'receipt' => $savepayment->receipt,
//                     'rateadjustment' => 0,
//                     'rid' => 'uc',
//                 ]);
//                 $savereservation->save();

    //                 return response()->json(['success' => true]);
// }    
//             default:
//                 return response()->json(['message' => 'Invalid payment type.'], 400);
//         }
//     }

    // public function makeCurlRequest($url, $data)
    // {
    //     $ch = curl_init($url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/x-www-form-urlencoded', 'X-Recurring-Api-Version: 1.0']);
    //     $responseContent = curl_exec($ch);
    //     curl_close($ch);

    //     if ($responseContent === false) {
    //         return ['error' => 'Error communicating with payment gateway.'];
    //     }

    //     parse_str($responseContent, $responseArray);
    //     return $responseArray;
    // }

    public function makeCurlRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/x-www-form-urlencoded', 'X-Recurring-Api-Version: 1.0']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $responseContent = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($responseContent === false) {
            return ['error' => 'Error communicating with payment gateway. Curl error: ' . $error];
        }

        parse_str($responseContent, $responseArray);
        return $responseArray;
    }

    public function postTerminalPayment(Request $request, $id)
    {
        $cart_reservation = CartReservation::findOrFail($id);
        $amount = $request->input('amount');
        $apiKey = config('services.cardknox.api_key');

        $data = [
            'xKey' => $apiKey,
            'xAmount' => $amount,
            'xDeviceName' => 'BBPOS',
            'xDeviceComPort' => 'COM9',
            'xDeviceBaud' => '115200',
            'xDeviceParity' => 'None',
            'xDeviceDataBits' => '8',
            'xDeviceTimeOut' => '60',
            'xEnableDeviceSwipe' => '1',
            'xEnableAmountConfirmationPrompt' => '1',
            'xResponseFormat' => 'JSON',
            'xExitFormIfApproved' => '1',
            'xCommand' => 'cc:encrypt',
        ];

        $url = 'https://x2.cardknox.com/gateway';
        $responseArray = $this->makeCurlRequest($url, $data);

        if (isset($responseArray['error'])) {
            return response()->json(['success' => false, 'message' => $responseArray['error']]);
        }

        if (isset($responseArray['xStatus']) && $responseArray['xStatus'] == 'Success') {
            return response()->json(['success' => true, 'transactionId' => $responseArray['xTransactionId']]);
        } else {
            return response()->json(['success' => false, 'message' => $responseArray['xMessage'] ?? 'Transaction failed']);
        }
    }

   

    public function checkPaymentStatus($id)
    {
        $transactionId = '123';

        $data = [
            'xKey' => config('services.cardknox.api_key'),
            'xCommand' => 'cc:get',
            'xTransactionId' => $transactionId,
        ];

        $ch = curl_init('https://x2.cardknox.com/gateway');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-type: application/x-www-form-urlencoded',
            'X-Recurring-Api-Version: 1.0'
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return response()->json(['paymentStatus' => 'Error', 'message' => 'Curl error: ' . $error]);
        }

        $response = json_decode($response, true);
        if (isset($response['xStatus']) && $response['xStatus'] == 'Approved') {
            return response()->json(['paymentStatus' => 'Success']);
        } else {
            return response()->json(['paymentStatus' => 'Pending']);
        }
    }

    public function storePayment(Request $request, $id)
    {
        $cart_reservation = CartReservation::findOrFail($id);
        $customer = Customer::find($cart_reservation->customernumber);
        $randomReceiptID = rand(1000, 9999);
        $apiKey = config('services.cardknox.api_key');
        $paymentType = $request->paymentType;
        $amount = $request->input('xAmount');

        $data = [
            'xKey' => $apiKey,
            'xVersion' => '4.5.5',
            'xAmount' => $amount,
            'xSoftwareVersion' => '1.0',
            'xSoftwareName' => 'KayutaLake',
        ];

        switch ($paymentType) {
            case 'Cash':
            case 'Other':
                $this->handleCashOrOtherPayment($cart_reservation, $customer, $request, $randomReceiptID, $paymentType);
                break;

            case 'Check':
                $data['xCommand'] = 'check:Sale';
                $data['xAccount'] = $request->input('xCheckNum');
                return $this->handleCardknoxPayment($data, $cart_reservation, $customer, $request, $randomReceiptID, $paymentType);

            case 'Manual':
            case 'Terminal':
                $data['xCommand'] = 'cc:Sale';
                $data['xCardNum'] = $request->input('xCardNum');
                $data['xExp'] = str_replace('/', '', $request->input('xExp'));
                return $this->handleCardknoxPayment($data, $cart_reservation, $customer, $request, $randomReceiptID, $paymentType);

            case 'Gift Card':
                $this->handleCashOrOtherPayment($cart_reservation, $customer, $request, $randomReceiptID, $paymentType);
                break;

            default:
                return response()->json(['message' => 'Invalid payment type'], 400);
        }

        return response()->json(['success' => true]);
    }

    private function handleCashOrOtherPayment($cart_reservation, $customer, $request, $randomReceiptID, $paymentType)
    {
        $payment = new Payment([
            'cartid' => $request->cartid,
            'receipt' => $randomReceiptID,
            'method' => $paymentType,
            'customernumber' => $cart_reservation->customernumber,
            'description' => $request->description ?? '',
            'checknumber' => $request->xCheckNum ?? '',
            'email' => $cart_reservation->email,
            'payment' => $request->xCash,
        ]);
        $payment->save();

        $this->saveReservation($cart_reservation, $customer, $randomReceiptID, $request);
    }

    private function handleCardknoxPayment($data, $cart_reservation, $customer, $request, $randomReceiptID, $paymentType)
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
                'payment' => $cart_reservation->total,
            ]);
            $payment->save();

            $this->saveReservation($cart_reservation, $customer, $randomReceiptID, $request);

            return response()->json(['success' => true]);
        } else {
            return response()->json(['message' => 'Payment failed: ' . ($responseArray['xError'] ?? 'Unknown error')], 400);
        }
    }

    private function saveReservation($cart_reservation, $customer, $randomReceiptID, $request)
    {
        $reservation = new Reservation([
            'cartid' => $request->cartid,
            'source' => 'Walk In',
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
            'xconfnum' => 0,
            'createdby' => 'Admin',
            'receipt' => $randomReceiptID,
            'rateadjustment' => 0,
            'rid' => 'uc',
        ]);
        $reservation->save();
    }



    public function payBalance(Request $request, $cartid)
    {
        $apiKey = config('services.cardknox.api_key');
        $cardNumber = $request->input('xCardNum');
        $xExp = str_replace('/', '', $request->xExp);

        $data = [
            'xKey' => $apiKey,
            'xVersion' => '4.5.5',
            'xCommand' => 'cc:Sale',
            'xAmount' => $request->xBalance,
            'xCardNum' => $cardNumber,
            'xExp' => $xExp,
            'xSoftwareVersion' => '1.0',
            'xSoftwareName' => 'KayutaLake',
        ];

        $payment = Payment::where('cartid', $cartid)->firstOrFail();
        if ($request->paymentType === 'Cash' || $request->paymentType === 'Other') {
            $payment->payment += $request->xCash;
            $payment->save();
            return response()->json(['success' => true]);
        } elseif ($request->paymentType === 'Manual') {
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
                $payment->payment += $request->xBalance;
                $payment->save();
                return response()->json(['success' => true, 'Payment Process' => $responseArray]);
            } else {
                Log::error('Payment failed', ['response' => $responseArray]);
                return response()->json(
                    [
                        'message' => 'Payment failed: ' . ($responseArray['xError'] ?? 'Unexpected error occurred.'),
                    ],
                    400,
                );
            }
        }
    }
}
