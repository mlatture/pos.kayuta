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
use App\Models\RigTypes;
use Illuminate\Http\Request;
use App\Models\CartReservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Survey;
use App\Models\PublishSurveyModel;
use App\Models\SurveysResponseModel;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

class NewReservationController extends Controller
{
    protected $reservation;
    protected $site;
    protected $payment;
    protected $customer;
    protected $cartReservation;
    protected $siteClass;
    protected $siteHookup;

    public function __construct(Reservation $reservation, Site $site, Payment $payment, Customer $customer, CartReservation $cartReservation, SiteClass $siteClass, SiteHookup $siteHookup)
    {
        $this->middleware('admin_has_permission:' . config('constants.role_modules.reservation_management.value'));
        $this->reservation = $reservation;
        $this->site = $site;
        $this->payment = $payment;
        $this->customer = $customer;
        $this->cartReservation = $cartReservation;
        $this->siteClass = $siteClass;
        $this->siteHookup = $siteHookup;
    }

    public function reservationHistory(Request $request, $id)
    {
        $reservations = $this->reservation->where('customernumber', $id)->select(['cid', 'cod', 'siteid', 'cartid', 'status', 'rigtype', 'riglength']);
        return DataTables::of($reservations)
            ->editColumn('cid', function ($reservation) {
                return Carbon::parse($reservation->cid)->format('M d, Y');
            })
            ->editColumn('cod', function ($reservation) {
                return Carbon::parse($reservation->cod)->format('M d, Y');
            })
            ->make(true);
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

        try {
            $reservation = Reservation::where('cartid', $cartid)->firstOrFail();
            $reservation->update([
                'checkedin' => Carbon::now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Reservation checked in successfully.', 'checked_in_date' => $reservation->checkedin->format('Y-m-d H:i:s')], 200);
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to checked-in status',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function updateCheckedOut(Request $request)
    {
        $cartid = $request->input('cartid');
        $request->validate([
            'cartid' => 'required|exists:reservations,cartid',
        ]);

        try {
            $reservation = Reservation::where('cartid', $cartid)->firstOrFail();
            $reservation->update([
                'checkedout' => Carbon::now(),
            ]);

            do {
                $generate_token = str_replace(['+', '/', '='], '', base64_encode(random_bytes(16)));
                $check_token = SurveysResponseModel::where('token', $generate_token)->exists();
            } while ($check_token);

            $publishedSurveyIds = PublishSurveyModel::where('active', true)->pluck('id');

            Survey::create([
                'name' => $reservation->fname . ' ' . $reservation->lname,
                'survey_id' => $publishedSurveyIds->toJson(),
                'guest_email' => $reservation->email,
                'token' => $generate_token,
                'siteId' => $reservation->siteid,
                'subject' => 'We value your feedback!',
                'message' => 'Thanks for your recent visit to Kayuta Lake Campground. We`d love to get your feedback on your stay.',
                'created_at' => Carbon::now()->addDay(),
            ]);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Reservation checked out successfully.',
                    'checked_out_date' => $reservation->checkedout->format('Y-m-d H:i:s'),
                ],
                200,
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to checked-out status',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function getReservations(Request $request)
    {
        // $limit = $request->input('limit', 10);
        // $siteId = $request->input('siteid');
        // $types = $request->input('tier', []);
        // $startDate = $request->input('start_date');
        // $endDate = $request->input('end_date');

        // $paymentCartIds = Payment::pluck('cartid')->toArray();

        // $reservationsQuery = Reservation::join('customers', 'reservations.customernumber', '=', 'customers.id')
        //     ->join('payments', 'reservations.cartid', '=', 'payments.cartid')
        //     ->whereIn('reservations.cartid', $paymentCartIds)
        //     ->select('reservations.*', 'customers.phone', 'customers.address', 'payments.payment')
        //     ->orderByDesc('reservations.id')
        //     ->distinct('reservations.id');

        // if($siteId){
        //     $reservationsQuery->where('reservations.siteid', $siteId);
        // }

        // if(!empty($types)){
        //     $reservationsQuery->whereIn('reservations.siteclass', $types);
        // }

        // if($startDate && $endDate){
        //     $reservationsQuery->where(function ($query) use ($startDate, $endDate) {
        //         $query->whereDate('reservations.cid', '>=', $startDate)
        //             ->whereDate('reservations.cod', '<=', $endDate);
        //     });
        // }elseif ($startDate) {
        //     $reservationsQuery->whereDate('reservations.cid', '>=', $startDate);
        // } elseif ($endDate) {
        //     $reservationsQuery->whereDate('reservations.cod', '<=', $endDate);
        // }

        // $reservations = $reservationsQuery->paginate($limit);
        // return response()->json($reservations);

        $siteIds = [];
        if ($request->site_names) {
            $siteIds = array_merge($siteIds, explode(',', $request->site_names));
        }
        if ($request->site_classes) {
            $siteIds = array_merge($siteIds, explode(',', $request->site_classes));
        }
        $siteIds = array_unique($siteIds);
        if ($request->date) {
            $date = explode('-', $request->date);
            if (count($date) > 1) {
                $filters['startDate'] = date('Y-m-d', strtotime($date[0]));
                $filters['endDate'] = date('Y-m-d', strtotime($date[1]));
            }
        } else {
            $filters['startDate'] = date('Y-m-01');
            $filters['endDate'] = date('Y-m-t');
        }
        $allSites = $this->site->getAllSiteWithReservations([], $filters, [])->get();
        $sites = collect();
        if ($siteIds) {
            $sites = $this->site->getAllSiteWithReservations([], $filters, $siteIds)->get();
            //            dd($sites);
        } else {
            $sites = $allSites;
        }

        $calendar = generateLinearCalendar($filters['startDate'], $filters['endDate']);

        foreach ($sites as $site) {
            $totalDays = 0;
            foreach ($site->reservations as $reservation) {
                $totalDays += Carbon::parse($reservation->cid)->diffInDays($reservation->cod);
            }
            $site->totalDays = $totalDays;
        }

        $allReservations = Reservation::orderBy('id', 'desc')->get();
        $allCurrentSites = Site::orderBy('id', 'desc')->get();
        $payments = $this->payment::whereIn('cartid', $allReservations->pluck('cartid')->toArray())->get();
        $customers = Customer::whereIn('id', $allReservations->pluck('customernumber')->toArray())->get();
        return response()->json([
            'allReservations' => $allReservations,
            'allCurrentSites' => $allCurrentSites,
            'sites' => $sites,
            'calendar' => $calendar,
            'allSites' => $allSites,
            'payments' => $payments,
            'customers' => $customers,
        ]);
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

        $reservedSiteIds = Reservation::where('cid', '<', $toDate)->where('cod', '>', $fromDate)->pluck('siteid')->toArray();

        $siteQuery = Site::whereNotIn('siteid', $reservedSiteIds);

        if ($request->has('siteclass') && !empty($request->siteclass)) {
            $siteclassArray = explode(',', trim($request->siteclass));
            $siteclasses = array_map(function ($value) {
                return str_replace(' ', '_', trim($value));
            }, $siteclassArray);

            if (!empty($siteclasses)) {
                $siteQuery->where(function ($query) use ($siteclasses, $request) {
                    if (in_array('RV_Sites', $siteclasses)) {
                        $query->where(function ($q) use ($request) {
                            $q->where('siteclass', 'RV_Sites')->orWhere('siteclass', 'RV_Sites,Tent_Sites');
                            if ($request->has('hookup') && !empty($request->hookup)) {
                                $hookup = $request->hookup;
                                $q->where('hookup', $hookup);
                            }
                        });

                        $siteclasses = array_diff($siteclasses, ['RV_Sites']);
                    }

                    if (in_array('Tent_Sites', $siteclasses)) {
                        $query->orWhere(function ($q) {
                            $q->where('siteclass', 'Tent_Sites')->orWhere('siteclass', 'RV_Sites,Tent_Sites');
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

    public function paymentIndex(Request $request, $confirmationNumber)
    {
        $reservations = CartReservation::where('cartid', $confirmationNumber)->get();
        $rigTypes = RigTypes::all();
        if ($reservations->isEmpty()) {
            abort(404, 'No reservations found.');
        }

        $customerNumber = $reservations->first()->customernumber;

        $customers = User::where('id', $customerNumber)->first();

        return view('reservations.payment', compact('reservations', 'rigTypes', 'customers'));
    }

    public function invoice(Request $request, $confirmationNumber)
    {
        $payment = Payment::where('cartid', $confirmationNumber)->firstOrFail();
        $cart = CartReservation::where('cartid', $confirmationNumber)->firstOrFail();
        $reservations = Reservation::where('cartid', $payment->cartid)->firstOrFail();

        return view('reservations.payment', compact('payment', 'reservations', 'cart'));
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
                'user_id' => 0,
            ],
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
            'sitelock' => $request->siteLock === 'on' ? 1 : 20,
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
            'transaction_type' => $request->status,
        ]);

        // $this->saveReservation($cart_reservation, $customer, $randomReceiptID, $request);

        return response()->json([
            'success' => true,
            'message' => 'Payment processed and reservation saved successfully.',
        ]);
    }

    public function storePayment(Request $request, $id)
    {
        $cart_reservation = CartReservation::where('cartid', $id)->firstOrFail();
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
                $this->handleCashOrOtherPayment($cart_reservation, $request, $randomReceiptID, $paymentType);
                break;

            case 'Check':
                $data['xCommand'] = 'check:sale';
                $data['xAccount'] = $request->input('xAccount');
                $data['xRouting'] = $request->input('xRouting');
                $data['xName'] = $request->input('xName');
                return $this->handleCardknoxPayment($data, $cart_reservation, $request, $randomReceiptID, $paymentType, $uniqueTransactionId);

            case 'Manual':
                $data['xCommand'] = 'cc:sale';
                $data['xCardNum'] = $request->input('xCardNum');
                $data['xExp'] = str_replace('/', '', $request->input('xExp'));
                return $this->handleCardknoxPayment($data, $cart_reservation, $request, $randomReceiptID, $paymentType, $uniqueTransactionId);

            case 'Gift Card':
                $amount = $request->input('xAmount');
                $barcode = $request->input('xBarcode');
                $this->handleGiftCardPayment($cart_reservation, $request, $randomReceiptID, $paymentType, $barcode, $amount);
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

        // $this->saveReservation($cart_reservation, $customer, $randomReceiptID, $request);
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

        // $this->saveReservation($cart_reservation, $customer, $randomReceiptID, $request);
    }

    private function handleCardknoxPayment($data, $cart_reservation, $request, $randomReceiptID, $paymentType, $uniqueTransactionId)
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
        Log::info('Cardknox Response:', $responseArray);

        if (isset($responseArray['xStatus']) && $responseArray['xStatus'] === 'Approved') {
            $payment = new Payment([
                'cartid' => $request->cartid,
                'receipt' => $randomReceiptID,
                'method' => $paymentType,
                'customernumber' => $cart_reservation->customernumber,
                'email' => $cart_reservation->email,
                'payment' => $request->xAmount,
                'transaction_type' => $request->status,
                'x_ref_num' => $responseArray['xRefNum'] ?? null,
            ]);
            $payment->save();

            $this->saveReservation($cart_reservation, $randomReceiptID, $request);
            if ($paymentType === 'Manual') {
                $this->saveCardonFiles($cart_reservation, $randomReceiptID, $request, $uniqueTransactionId);
            }
            return response()->json(['success' => true]);
        } else {
            return response()->json(['message' => 'Payment failed: ' . ($responseArray['xError'] ?? 'Unknown error')], 400);
        }
    }

    private function saveReservation($cart_reservation, $randomReceiptID, $request)
    {
        $customer = User::where('id', $cart_reservation->customernumber)->first();
        $reservation = new Reservation([
            'cartid' => $request->cartid,
            'source' => 'Reservation',
            'email' => $cart_reservation->email,
            'fname' => $customer->f_name,
            'lname' => $customer->l_name,
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

    private function saveCardonFiles($cart_reservation, $randomReceiptID, $request, $uniqueTransactionId)
    {
        $fullCardNumber = $request->xCardNum;
        $maskedCardNumber = substr($fullCardNumber, 0, 1) . str_repeat('*', strlen($fullCardNumber) - 1) . substr($fullCardNumber, -3);
        $cardType = $this->getCardType($fullCardNumber);

        try {
            $cardonFiles = new CardsOnFile([
                'customernumber' => $cart_reservation->cartid,
                'cartid' => $cart_reservation->cartid,
                'method' => $cardType,
                'receipt' => $randomReceiptID,
                'email' => $cart_reservation->email,
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

    public function reservationInCart()
    {
        $reservations = CartReservation::with('user')->get();
        return view('cart-reservations.index', compact('reservations'));
    }

    public function quoteSite(Request $request)
    {
        $siteIds = array_unique($request->input('siteIds', []));
        $nights = $request->input('nights', 1);
        $sites = Site::whereIn('id', $siteIds)
            ->distinct()
            ->get(['id', 'ratetier', 'siteid']);

        $quotes = [];

        foreach ($sites as $site) {
            $rateTier = RateTier::where('tier', $site->ratetier)->first();

            $rate = 0;
            if ($rateTier) {
                if ($nights < 7) {
                    $rate = $rateTier->flatrate * $nights;
                } elseif ($nights == 7) {
                    $rate = $rateTier->weeklyrate;
                } else {
                    $extraNights = $nights - 7;
                    $rate = $rateTier->weeklyrate + $extraNights * $rateTier->flatrate;
                }
            }

            $quotes[] = [
                'id' => $site->id,
                'siteid' => $site->siteid,
                'rate' => $rate,
                'rate_tier' => $rateTier ? $rateTier->tier : 'Unknown',
            ];
        }

        return response()->json($quotes);
    }

    public function patchAvailability(Request $request)
    {
        $siteIds = $request->input('siteIds', []);

        $sites = Site::whereIn('id', $siteIds)->get();

        foreach ($sites as $site) {
            $site->update([
                'available' => 0,
                'availableonline' => 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Availability updated successfully!',
        ]);
    }

    public function createReservation(Request $request)
    {
        do {
            $confirmationNumber = 'CN' . rand(100000, 999999);
        } while (CartReservation::where('cartid', $confirmationNumber)->exists());

        $siteIds = $request->input('siteIds', []);
        $rate = $request->input('rate', []);
        $total = $request->input('total', 0);
        $nights = $request->input('nights', 1);
        $checkin = $request->input('checkin', now()->toDateString());
        $checkout = $request->input('checkout', now()->addDays($nights)->toDateString());

        $sites = Site::whereIn('id', $siteIds)->get();

        $cart_reservation = [];

        foreach ($sites as $site) {
            $rateTier = RateTier::where('tier', $site->ratetier)->first();

            $rate = 0;
            $description = 'Charge for site: ' . $site->siteid;

            if ($rateTier) {
                if ($nights < 7) {
                    $rate = $rateTier->flatrate * $nights;
                    $description .= " - Nightly rate for $nights night(s)";
                } elseif ($nights == 7) {
                    $rate = $rateTier->weeklyrate;
                    $description .= ' - Weekly rate applied';
                } else {
                    $extraNights = $nights - 7;
                    $rate = $rateTier->weeklyrate + $extraNights * $rateTier->flatrate;
                    $description .= " - Weekly rate plus $extraNights extra night(s)";
                }
            }

            $cart_reservation[] = [
                'cartid' => $confirmationNumber,
                'siteid' => $site->siteid,
                'siteclass' => $site->siteclass,
                'description' => $description,
                'base' => $rate,
                'cid' => $checkin,
                'cod' => $checkout,
                'nights' => $nights,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        CartReservation::insert($cart_reservation);

        return response()->json([
            'confirmationNumber' => $confirmationNumber,
            'sites' => $sites,
            'nights' => $nights,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'rate' => $rate,
            'total' => $total,
        ]);
    }

    public function lookupCustomer(Request $request)
    {
        $query = $request->input('search');

        if (!isset($query) || empty($query)) {
            return response()->json([]);
        }

        $users = User::where('f_name', 'LIKE', "%{$query}%")
            ->orWhere('l_name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->orWhere('customer_number', 'LIKE', "%{$query}%")
            ->get(['id', 'organization_id', 'f_name', 'l_name', 'name', 'phone', 'email', 'date_of_birth', 'anniversary', 'age', 'street_address', 'address_2', 'address_3', 'country', 'city', 'zip', 'state', 'home_phone', 'work_phone', 'customer_number', 'driving_license', 'wallet_balance', 'loyalty_point']);

        $uniqueUsers = $users
            ->unique(function ($user) {
                return $user->email . '-' . $user->customer_number;
            })
            ->values();

        return response()->json($uniqueUsers);
    }

    public function createNewReservation(Request $request)
    {
        $data = $request->json()->all();
        $rigTypes = RigTypes::where('id', $data['rigtype'])->first();

        $cart_reservation = CartReservation::where('cartid', $data['confirmation'])->first();

        $customer = null;

        if (isset($data['first_name']) || isset($data['last_name']) || isset($data['email'])) {
            $customer = Customer::where(function ($query) use ($data) {
                if (isset($data['first_name'])) {
                    $query->orWhere('first_name', $data['first_name']);
                }
                if (isset($data['last_name'])) {
                    $query->orWhere('last_name', $data['last_name']);
                }
                if (isset($data['email'])) {
                    $query->orWhere('email', $data['email']);
                }
            })->first();
        }

        foreach ($data['customers'] as $customerData) {
            $existingCustomer = User::where('f_name', $customerData['first_name'])->where('l_name', $customerData['last_name'])->where('email', $customerData['email'])->first();

            if (!$existingCustomer) {
                Customer::create([
                    'first_name' => $customerData['first_name'],
                    'last_name' => $customerData['last_name'],
                    'email' => $customerData['email'],
                    'discovery_method' => $customerData['discovery_method'] ?? null,
                    'phone' => $customerData['phone'] ?? null,
                    'work_phone' => $customerData['work_phone'] ?? null,
                    'home_phone' => $customerData['home_phone'] ?? null,
                    'customer_number' => $customerData['customer_number'] ?? null,
                    'driving_license' => $customerData['driving_license'] ?? null,
                    'date_of_birth' => !empty($customerData['date_of_birth']) ? $customerData['date_of_birth'] : null,
                    'anniversary' => !empty($customerData['anniversary']) ? $customerData['anniversary'] : null,
                    'age' => !empty($customerData['age']) ? intval($customerData['age']) : null,
                    'address' => $customerData['address'] ?? null,
                    'address_2' => $customerData['address_2'] ?? null,
                    'address_3' => $customerData['address_3'] ?? null,
                    'city' => $customerData['city'] ?? null,
                    'state' => $customerData['state'] ?? null,
                    'zip' => $customerData['zip'] ?? null,
                    'country' => $customerData['country'] ?? null,
                    'user_id' => $customerData['user_id'] ?? null,
                ]);
            }

            $cart_reservation->update([
                'customernumber' => $existingCustomer ? $existingCustomer->id : null,
                'riglength' => !empty($data['length']) ? (int) $data['length'] : 0,
                'rigtype' => $rigTypes ? $rigTypes->rigtype : null,
                'sitelock' => $data['site_lock'],
                'subtotal' => !empty($data['subtotal']) ? floatval($data['subtotal']) : 0.0,
                'total' => (!empty($data['subtotal']) ? floatval($data['subtotal']) : 0.0) + ($data['site_lock'] ? 20 : 0),
                'number_of_guests' => !empty($data['number_of_guests']) ? (int) $data['number_of_guests'] : 0,
            ]);

            if (!$cart_reservation) {
                return response()->json(['error' => 'No reservations found'], 400);
            }

            $reservation = Reservation::create([
                'cartid' => $data['confirmation'],
                'source' => $data['source'],
                'email' => $existingCustomer ? $existingCustomer->email : null,
                'status' => $data['status'],
                'createdate' => $data['created_on'],
                'createdby' => $data['created_by'],
                'fname' => $existingCustomer ? $existingCustomer->first_name : null,
                'lname' => $existingCustomer ? $existingCustomer->last_name : null,
                'customernumber' => $existingCustomer ? $existingCustomer->id : null,
                'siteid' => $cart_reservation->siteid,
                'cid' => $cart_reservation->cid,
                'cod' => $cart_reservation->cod,
                'total' => $cart_reservation->total,
                'subtotal' => !empty($data['subtotal']) ? floatval($data['subtotal']) : 0.0,
                'siteclass' => $cart_reservation->siteclass,
                'nights' => $cart_reservation->nigths,
                'sitelock' => $cart_reservation->sitelock,
                'rigtype' => $rigTypes ? $rigTypes->rigtype : null,
                'xconfnum' => rand(100000, 999999),
            ]);
        }

        return response()->json([
            'success' => true,
            'reservation_id' => $reservation->id,
        ]);
    }

    public function clearAbandoned()
    {
        $now = Carbon::now('America/New_York');

        CartReservation::where('holduntil', '<', $now)->delete();

        return response()->json([
            'success' => true,
            'message' => 'All abandoned cart reservations have been cleared.',
        ]);
    }

    public function refund(Request $request)
    {
        $request->validate([
            'cartid' => 'required|exists:reservations,cartid',
            'siteid.*' => 'exists:reservations,siteid',
            'reason' => 'nullable|string',
            'refunded_amount' => 'required|numeric|min:0.01',
            'cancellation_fee' => 'nullable|numeric',
        ]);

        $payment = Payment::where('cartid', $request->cartid)
            ->where(function ($q) {
                $q->whereNull('transaction_type')->orWhere('transaction_type', '!=', 'REFUND');
            })
            ->whereNotNull('x_ref_num')
            ->latest()
            ->first();

        if (!$payment || !$payment->x_ref_num) {
            return response()->json(['message' => 'Original payment reference not found or x_ref_num is missing.'], 404);
        }

        foreach ($request->siteid as $siteid) {
            $payload = [
                'xKey' => config('services.cardknox.api_key'),
                'xVersion' => '5.0.0',
                'xSoftwareName' => 'KayutaLake',
                'xSoftwareVersion' => '1.0',
                'xCommand' => 'cc:refund',
                'xRefNum' => $payment->x_ref_num,
                'xAmount' => $request->refunded_amount,
            ];

            $response = Http::asForm()
                ->withHeaders([
                    'X-Recurring-Api-Version' => '1.0',
                ])
                ->post('https://x1.cardknox.com/gateway', $payload);

            parse_str($response->body(), $responseArray);

            if (($responseArray['xStatus'] ?? '') !== 'Approved') {
                return response()->json(
                    [
                        'message' => 'Refund failed: ' . ($responseArray['xError'] ?? 'Unknown error'),
                    ],
                    400,
                );
            }

            $payment->update([
                'transaction_type' => 'REFUND',
                'x_ref_num' => $responseArray['xRefNum'] ?? null,
                'cancellation_fee' => $request->cancellation_fee,
                'refunded_amount' => $request->refunded_amount,
            ]);

            Reservation::where('cartid', $request->cartid)
                ->where('siteid', $siteid)
                ->update([
                    'status' => 'Cancelled',
                    'reason' => $request->reason,
                ]);
        }

        return response()->json(['message' => 'Refund processed successfully for selected sites.']);
    }
}
