<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Site;
use App\Models\User;
use App\Models\SiteClass;
use App\Models\RateTier;
use App\Models\TaxType;
use App\Models\CartReservation;
use App\Models\Receipt;
use App\Models\GiftCard;
use App\Models\SiteHookup;
use App\Models\BusinessSettings;
use App\Models\Setting;
use App\Models\Infos;

use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Arr;

class ReservationManagementController extends Controller
{
    public function index()
    {
        $siteTypes = Site::select('id', 'sitename')->orderBy('sitename')->get();

        $siteClasses = SiteClass::orderBy('siteclass')->get();
        $siteHookups = SiteHookup::orderBy('orderby')->get();
        return view('reservations.management.index', compact('siteTypes', 'siteClasses', 'siteHookups'));
    }

    public function availability(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'view' => ['nullable', 'string'],
            'siteclass' => ['nullable', 'string'],
            'hookup' => ['nullable', 'string'],
            'amps' => ['nullable', 'integer'],
            // 'pets_ok' => ['sometimes', 'boolean'],
            'include_offline' => ['sometimes', 'boolean'],
            'include_reserved' => ['sometimes', 'boolean'],
            'include_seasonal' => ['sometimes', 'boolean'],
            'rig_length' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'with_prices' => ['sometimes', 'boolean'],
            'site_lock_fee' => ['nullable', 'string'],
        ]);

        $siteLockFee = BusinessSettings::where('type', 'site_lock_fee')->value('value') ?? 0;

        // Always include with_prices
        $validated['with_prices'] = true;
        $validated['view'] = 'units';

        // Filter out null or empty values to avoid confusing the API
        $query = collect($validated)
            ->filter(function ($value) {
                return !is_null($value) && $value !== '';
            })
            ->toArray();

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('BOOKING_BEARER_KEY'),
            ])->get(env('BOOK_API_URL') . 'v1/availability', $query);

            if (!$response->successful()) {
                Log::error('Availability API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json(
                    [
                        'ok' => false,
                        'message' => 'Failed to fetch availability from booking service.',
                        'status' => $response->status(),
                    ],
                    $response->status(),
                );
            }

            $data = $response->json();

            Log::info('Fetched True WebPayload', ['data' => $data]);

            if (isset($data['response']['results']['units'])) {
                $units = collect($data['response']['results']['units']);

                /**
                 * Exclude unavailable units
                 */

                $units = $units->filter(fn($unit) => isset($unit['status']['available']) && $unit['status']['available'] === true)->values();

                /**
                 *  Rig length filtering
                 */
                if (!empty($validated['rig_length'])) {
                    $riglength = (float) ($data['response']['filters']['rig_length'] ?? $validated['rig_length']);

                    $units = $units
                        ->filter(function ($unit) use ($riglength) {
                            $max = isset($unit['maxlength']) ? (float) $unit['maxlength'] : null;
                            if ($max === null) {
                                return false;
                            }

                            return $riglength <= $max;
                        })
                        ->values();

                    Log::info('Filtered availability by rig length', [
                        'rig_length' => $riglength,
                        'remaining_units' => $units->count(),
                    ]);
                }

                /**
                 *  Site class filtering
                 */
                if (!empty($validated['siteclass'])) {
                    $siteclass = str_replace(' ', '_', trim($validated['siteclass']));

                    $units = $units
                        ->filter(function ($unit) use ($siteclass) {
                            $classes = isset($unit['class']) ? collect(explode(',', $unit['class']))->map(fn($c) => str_replace(' ', '_', trim($c))) : collect();

                            return $classes->contains($siteclass);
                        })
                        ->values();

                    Log::info('Filtered availability by site class', [
                        'siteclass' => $siteclass,
                        'remaining_units' => $units->count(),
                    ]);
                }

                /**
                 *  Hookup filtering
                 */
                if (!empty($validated['hookup'])) {
                    $hookup = str_replace(' ', '_', trim($validated['hookup']));

                    $units = $units
                        ->filter(function ($unit) use ($hookup) {
                            $unitHookup = isset($unit['hookup']) ? str_replace(' ', '_', trim($unit['hookup'])) : null;
                            return $unitHookup === $hookup;
                        })
                        ->values();

                    Log::info('Filtered availability by hookup', [
                        'hookup' => $hookup,
                        'remaining_units' => $units->count(),
                    ]);
                }

                /**
                 * Reserved Site Filtering
                 * Default behavior: exclude reserved sites unless include_reserved = true
                 */
                $includeReserved = filter_var($validated['include_reserved'] ?? false, FILTER_VALIDATE_BOOLEAN);

                if (!$includeReserved) {
                    $units = $units
                        ->filter(function ($unit) {
                            $isReserved = isset($unit['status']['reserved']) ? (bool) $unit['status']['reserved'] : false;
                            return !$isReserved; // keep only not reserved
                        })
                        ->values();

                    Log::info('Filtered availability to exclude reserved sites', [
                        'remaining_units' => $units->count(),
                    ]);
                }

                /**
                 * Seasonal Filtering
                 * Default behavior: exclude seasonal sites
                 */

                $includeSeasonal = filter_var($validated['include_seasonal'] ?? false, FILTER_VALIDATE_BOOLEAN);

                if (!$includeSeasonal) {
                    $units = $units->filter(fn($unit) => !($unit['status']['is_seasonal'] ?? false))->values();

                    Log::info('Filtered seasonal ', [
                        'remaining_units' => $units->count(),
                    ]);
                }

                // /**
                //  * Offline Site Filtering
                //  * Default behavior: exclude offline sites
                //  */

                $includeOffline = filter_var($validated['include_offline'] ?? false, FILTER_VALIDATE_BOOLEAN);

                if (!$includeOffline) {
                    // Exclude offline sites (available_online = false / 0)
                    $units = $units
                        ->filter(function ($unit) {
                            $availableOnline = $unit['status']['available_online'] ?? ($unit['status']['availableonline'] ?? 0);
                            return (bool) $availableOnline; // Only keep online units
                        })
                        ->values();

                    Log::info('Filtered offline sites', [
                        'remaining_units' => $units->count(),
                    ]);
                }

                //  Update the response
                $data['response']['results']['units'] = $units->values()->all();
                $data['response']['results']['total_units'] = $units->count();
            }

            return response()->json(['data' => $data, 'site_lock_fee' => $siteLockFee, '']);
        } catch (\Exception $e) {
            Log::error('Availability proxy failed', ['error' => $e->getMessage()]);

            return response()->json(
                [
                    'ok' => false,
                    'message' => 'Error connecting to booking service.',
                ],
                500,
            );
        }
    }

    public function viewMap(Request $request, string $bookType = 'book_now')
    {
        $response = $this->availability($request);

        $data = $response instanceof JsonResponse ? $response->getData(true) : $response;

        $sites = $data['data']['sites'] ?? [];
        $hookup = $request->hookup ?? null;
        $riglength = (int) ($request->riglength ?? 0);
        $siteclass = $request->siteclass ?? '';
        $cartIds = $data['data']['cartIds'] ?? [];

        $processedSites = [];

        foreach ($sites as $currentsite) {
            $csiteclassArray = array_map(fn($v) => strtolower(str_replace('_', ' ', trim($v))), explode(',', $currentsite['class'] ?? ''));

            $isThisAnRvSite = in_array('rv sites', $csiteclassArray);
            $classMatch = in_array(strtolower($siteclass), $csiteclassArray);

            $rigLength = $riglength;
            $minLength = (int) ($currentsite['minlength'] ?? 0);
            $maxLength = (int) ($currentsite['maxlength'] ?? 0);

            $isAvailable = (bool) ($currentsite['available'] ?? false);
            $isAvailableOnline = (bool) ($currentsite['availableonline'] ?? false);
            $isSeasonal = (bool) ($currentsite['seasonal'] ?? false);
            $isUnavailable = !$isAvailable || !$isAvailableOnline;

            // Default values
            $fillcolor = '#66FF66';
            $disableLink = false;
            $filltext = "$hookup Max Length is $maxLength feet. This site is available, click to review details";

            if (!$classMatch) {
                $fillcolor = 'red';
                $filltext = 'This is not the type of site you are looking for.';
                $disableLink = true;
            } elseif ($isSeasonal || trim($currentsite['reserved'] ?? '') !== 'Available') {
                $fillcolor = 'red';
                $filltext = 'This site is reserved';
                $disableLink = true;
            } elseif ($isUnavailable) {
                $fillcolor = 'red';
                $filltext = 'This site is not available';
                $disableLink = true;
            }

            if ($isThisAnRvSite && ($rigLength < $minLength || $rigLength > $maxLength)) {
                $fillcolor = 'red';
                $filltext = 'Your rig will not fit.';
                $disableLink = true;
            }

            if ($isThisAnRvSite && $hookup && ($currentsite['hookup'] ?? '') !== $hookup) {
                $fillcolor = 'orange';
                $filltext = "This site does not have the requested hookup $hookup";
                $disableLink = false;
            }

            $processedSites[] = array_merge($currentsite, [
                'fillcolor' => $fillcolor,
                'disableLink' => $disableLink,
                'filltext' => $filltext,
                'csiteclassArray' => $csiteclassArray,
            ]);
        }

        // Save booking session
        Session::put('booking', [
            'cid' => $request->start_date ?? '',
            'cod' => $request->end_date ?? '',
            'hookup' => $hookup,
            'riglength' => $riglength,
            'siteclass' => $siteclass,
        ]);

        return view('reservations.management.map.booking', [
            'sites' => $processedSites,
            'booking' => Session::get('booking'),
            'cartids' => $cartIds,
        ]);
    }

    public function viewSiteDetails(Request $request)
    {
        $data = $request->validate([
            'site_id' => ['required', 'string'],
            'uscid' => ['required', 'date'],
            'uscod' => ['required', 'date'],
        ]);

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('BOOKING_BEARER_KEY'),
            ])->get(env('BOOK_API_URL') . "v1/sites/{$data['site_id']}", $data);

            if ($response->successful()) {
                return response()->json($response->json(), 200);
            }
        } catch (\Exception $e) {
            Log::error('Site details proxy failed', ['error' => $e->getMessage()]);

            return response()->json(
                [
                    'ok' => false,
                    'message' => 'Error connecting to booking service.',
                ],
                500,
            );
        }
    }

    public function information()
    {
        $information = Infos::where('show_in_details', 1)->orderBy('id', 'asc')->get();

        return response()->json(['information' => $information]);
    }

    public function cart(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('BOOKING_BEARER_KEY'),
            ])->post(env('BOOK_API_URL') . 'v1/cart', [
                'utm_source' => 'rvparkhq',
                'utm_medium' => 'referral',
                'utm_campaign' => 'summer',
            ]);

            if ($response->successful()) {
                return response()->json($response->json(), 200);
            }

            Log::error('Cart API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json(
                [
                    'ok' => false,
                    'message' => 'Failed to create cart from booking service.',
                    'status' => $response->status(),
                ],
                $response->status(),
            );
        } catch (\Exception $e) {
            Log::error('Cart API proxy failed', ['error' => $e->getMessage()]);

            return response()->json(
                [
                    'ok' => false,
                    'message' => 'Error connecting to booking service.',
                ],
                500,
            );
        }
    }

    public function getCart(Request $request)
    {
        // ---------------------------------------------------------
        // PATCH: Support for Local Modification Carts (MOD-...)
        // ---------------------------------------------------------
        if (str_starts_with($request['cart_id'], 'MOD-')) {
            $localItems = CartReservation::where('cartid', $request['cart_id'])->get();
            
            if ($localItems->isEmpty()) {
                return response()->json(['ok' => false, 'message' => 'Modification cart not found locally.'], 404);
            }

            // Map local items to API response structure
            $items = $localItems->map(function($item) {
                return [
                    'cart_item_id' => $item->id,
                    'site_id' => $item->siteid,
                    'is_lock' => false,
                    'price_quote' => [
                        'total' => (float)$item->total,
                        'subtotal' => (float)$item->subtotal,
                        'avg_nightly' => 0, // Not applicable for credit
                    ],
                    // Add other fields if JS needs them
                    'name' => $item->siteid === 'CREDIT' ? 'Modification Credit' : $item->siteid,
                    'hookup' => 'N/A',
                    'minlength' => 0,
                    'maxlength' => 0,
                ];
            });

            // Calculate totals
            $subtotal = $localItems->sum('subtotal');
            $total = $localItems->sum('total');

            return response()->json([
                'ok' => true,
                'data' => [
                    'cart' => [
                        'cart_id' => $request['cart_id'],
                        'cart_token' => $request['cart_token'] ?? 'mod_token',
                        'items' => $items,
                        'financials' => [
                             'subtotal' => $subtotal,
                             'total' => $total,
                             'taxes' => 0
                        ]
                    ]
                ]
            ]);
        }
        // ---------------------------------------------------------

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('BOOKING_BEARER_KEY'),
            ])->get(env('BOOK_API_URL') . "v1/cart/{$request['cart_id']}", [
                'cart_token' => $request['cart_token'],
            ]);

            if ($response->successful()) {
                return response()->json($response->json(), 200);
            }

            Log::error('Get Cart API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json(
                [
                    'ok' => false,
                    'message' => 'Failed to get cart from booking service.',
                    'status' => $response->status(),
                ],
                $response->status(),
            );
        } catch (\Exception $e) {
            Log::error('Get Cart API proxy failed', ['error' => $e->getMessage()]);

            return response()->json(
                [
                    'ok' => false,
                    'message' => 'Error connecting to booking service.',
                ],
                500,
            );
        }
    }

    public function cartItems(Request $request)
    {
        // ---------------------------------------------------------
        // PATCH: Support for Local Modification Carts (MOD-...)
        // ---------------------------------------------------------
        if (str_starts_with($request['cart_id'], 'MOD-')) {
             try {
                // Remove existing validation for integer cart_id temporarily or manually validating
                // Since this block runs before validation, we do validtion inside or assume input is ok from frontend
                
                 $site = \App\Models\Site::where('siteid', $request->site_id)->first();
                 // Simplification: Using a flat rate or fetching from RateTier if possible
                 $price = 50; // Fallback
                 if ($site) {
                     $tier = \App\Models\RateTier::where('siteclass', $site->class)->first();
                     if ($tier) $price = $tier->daily_rate ?? 50;
                 }
                 
                 // Calculate Nights
                 $startDate = Carbon::parse($request->start_date);
                 $endDate = Carbon::parse($request->end_date);
                 $nights = $startDate->diffInDays($endDate);
                 $subTotal = $price * $nights;
                 
                  // Add Site Lock Fee if applicable
                 $lockFee = ($request->site_lock_fee == 'on') 
                    ? (BusinessSettings::where('type', 'site_lock_fee')->value('value') ?? 0)
                    : 0;
                 
                 $total = $subTotal + $lockFee; 
                 // Note: Frontend JS adds site lock fee to subtotal for display? 
                 // Actually frontend logic sums price_quote.total.

                 CartReservation::create([
                    'customernumber' => 0, // Guest/User will be attached at checkout or derived
                    'cid' => $startDate,
                    'cod' => $endDate,
                    'cartid' => $request['cart_id'],
                    'siteid' => $request->site_id,
                    'description' => "Reservation for {$request->site_id}",
                    'base' => $price,
                    'subtotal' => $subTotal,
                    'total' => $total,
                    'taxrate' => 0, 
                    'totaltax' => 0,
                    'nights' => $nights,
                    'rid' => '',
                    'holduntil' => now()->addMinutes(15), 
                    'people' => 1,
                    'pets' => 0,
                    'email' => 'modification@temp.com' // Temp email
                 ]);

                 return response()->json([
                     'ok' => true,
                     'status' => 200,
                     'data' => [
                        'status' => 'success',
                        'cart_items' => [] // Frontend reloads via getCart
                     ],
                     'message' => 'Items added to local modification cart successfully.',
                 ]);

             } catch (\Exception $e) {
                 Log::error("Local Cart Add Failed", ['error' => $e->getMessage()]);
                 return response()->json(['ok' => false, 'message' => 'Failed to add to local cart.'], 500);
             }
        }
        // ---------------------------------------------------------

        $data = $request->validate([
            'cart_id' => ['required', 'string'], // Changed to string to allow MOD- if patch misses or future
            'token' => ['required', 'string'],
            'site_id' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'occupants' => ['nullable', 'array'],
            'add_ons' => ['nullable', 'array'],
            'price_quote_id' => ['nullable', 'string'],
            'site_lock_fee' => 'sometimes|in:on,off',
        ]);

        if ($data['site_lock_fee'] == 'on') {
            $siteLockFee = BusinessSettings::where('type', 'site_lock_fee')->value('value') ?? 0;
        } else {
            $siteLockFee = 0;
        }

        $data['site_lock_fee'] = (float) $siteLockFee;

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('BOOKING_BEARER_KEY'),
            ])->post(env('BOOK_API_URL') . 'v1/cart/items', $data);

            Log::error('Cart items API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->failed()) {
                return response()->json(
                    [
                        'ok' => false,
                        'message' => 'Failed to add items to cart.',
                        'status' => $response->status(),
                    ],
                    $response->status(),
                );
            }

            return response()->json(
                [
                    'ok' => true,
                    'status' => $response->status(),
                    'data' => $response->json(),
                    'message' => 'Items added to cart successfully.',
                ],
                $response->status(),
            );
        } catch (\Exception $e) {
            Log::error('Cart items proxy failed', ['error' => $e->getMessage()]);

            return response()->json(
                [
                    'ok' => false,
                    'message' => 'Error connecting to booking service.',
                ],
                500,
            );
        }
    }

    public function checkout(Request $request)
    {
        // ---------------------------------------------------------
        // PATCH: Support for Local Modification Carts (MOD-...)
        // ---------------------------------------------------------
        $cartId = $request->input('api_cart.cart_id');
        if ($cartId && str_starts_with($cartId, 'MOD-')) {
             try {
                // Map Payment Method and Fields
                $paymentMethod = $request->input('payment_method');
                $paymentType = '';
                $extraData = [
                    'cartid' => $cartId,
                    'xAmount' => $request->input('xAmount'),
                ];

                switch ($paymentMethod) {
                    case 'card':
                        $paymentType = 'Manual';
                        $extraData['xCardNum'] = $request->input('cc.xCardNum');
                        $extraData['xExp'] = $request->input('cc.xExp');
                        $extraData['cvv'] = $request->input('cc.cvv'); 
                        break;
                    case 'cash':
                        $paymentType = 'Cash';
                        $extraData['xCash'] = $request->input('cash_tendered'); // storePayment checks xCash? No, it uses xAmount for Payment model but maybe xCash for something else. Checking handleCashOrOtherPayment... it uses xAmount for payment model.
                        break;
                    case 'ach':
                        $paymentType = 'Check';
                        $extraData['xAccount'] = $request->input('ach.account');
                        $extraData['xRouting'] = $request->input('ach.routing');
                        $extraData['xName'] = $request->input('ach.name');
                        break;
                    case 'gift_card':
                        $paymentType = 'Gift Card';
                        $extraData['xBarcode'] = $request->input('gift_card_code');
                        break;
                    default:
                        $paymentType = 'Other';
                }

                $extraData['paymentType'] = $paymentType;

                // Merge into a new Request or the existing one
                $newRequest = $request->merge($extraData);

                // Call NewReservationController logic
                // We resolve it from container
                $controller = app(\App\Http\Controllers\NewReservationController::class);
                return $controller->storePayment($newRequest, $cartId);

             } catch (\Exception $e) {
                 Log::error("Local Checkout Failed", ['error' => $e->getMessage()]);
                 return response()->json(['ok' => false, 'message' => 'Failed to process local checkout: ' . $e->getMessage()], 500);
             }
        }
        // ---------------------------------------------------------

        $data = $request->validate([
            'payment_method' => ['required', Rule::in(['cash', 'ach', 'gift_card', 'card'])],
            'gift_card_code' => ['nullable', 'string', 'max:64'],

            'cc.xCardNum' => ['required_if:payment_method,card'],
            'cc.xExp' => ['required_if:payment_method,card'],
            'cc.cvv' => ['required_if:payment_method,card'],

            'ach.routing' => ['required_if:payment_method,ach'],
            'ach.account' => ['required_if:payment_method,ach'],
            'ach.name' => ['required_if:payment_method,ach'],

            'cash_tendered' => ['required_if:payment_method,cash'],
            'custId' => ['nullable', 'integer'],

            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'street_address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',

            'xAmount' => ['required', 'numeric'], // Removed min:0.5 to allow refunds via negative amount if logic permits, or we handle refund elsewhere. Actually NewReservationController handles refunds if amount is negative? MoneyActionController sets negative total on Credit. If cart total is negative, xAmount will be negative.
            'api_cart.cart_id' => ['required'],
            'api_cart.cart_token' => ['required'],
        ]);

        // Update customer if needed
        if (!empty($data['custId'])) {
            $user = User::find($data['custId']);
            if ($user) {
                $user->update([
                    'f_name' => $data['fname'],
                    'l_name' => $data['lname'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'street_address' => $data['street_address'],
                    'city' => $data['city'],
                    'state' => $data['state'],
                    'zip' => $data['zip'],
                ]);
            }
        }

        // Flatten card and ACH
        if ($data['payment_method'] === 'card') {
            $data['xCardNum'] = $data['cc']['xCardNum'];
            $data['xExp'] = $data['cc']['xExp'];
            $data['cvv'] = $data['cc']['cvv'];
            Arr::forget($data, 'cc');
        }

        

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('BOOKING_BEARER_KEY'),
            ])->post(env('BOOK_API_URL') . 'v1/checkout', $data);

            if ($response->failed()) {
                return response()->json(
                    [
                        'ok' => false,
                        'message' => $response->json()['message'] ?? '',
                        'errors' => $response->json()['errors'] ?? [],
                    ],
                    $response->status(),
                );
            }

            // Handle gift card deduction
            if ($data['payment_method'] === 'gift_card') {
                $res = $response->json();
                $total = $res['reservations'][0]['totalcharges'] ?? null;

                if ($total !== null) {
                    GiftCard::where('barcode', $data['gift_card_code'])->decrement('amount', $total);
                }
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Checkout proxy failed', ['error' => $e->getMessage()]);
            return response()->json(
                [
                    'ok' => false,
                    'message' => 'Error connecting to booking service.',
                ],
                500,
            );
        }
    }

    public function removeCartItem(Request $request)
    {
        // ---------------------------------------------------------
        // PATCH: Support for Local Modification Carts (MOD-...)
        // ---------------------------------------------------------
        if (str_starts_with($request['cart_id'], 'MOD-')) {
             try {
                CartReservation::where('id', $request['cart_item_id'])->delete();
                return response()->json([
                     'ok' => true,
                     'message' => 'Item removed from modification cart.'
                 ]);
             } catch (\Exception $e) {
                 return response()->json(['ok' => false, 'message' => 'Failed to remove local item.'], 500);
             }
        }
        // ---------------------------------------------------------

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('BOOKING_BEARER_KEY'),
            ])->delete(env('BOOK_API_URL') . 'v1/cart/items', [
                'cart_id' => $request->input('cart_id'),
                'cart_token' => $request->input('token'),
                'cart_item_id' => $request->input('cart_item_id'),
            ]);

            Log::info('Remove cart item response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                return response()->json($response->json(), 200);
            }
        } catch (\Exception $e) {
            Log::error('Cart item removal proxy failed', ['error' => $e->getMessage()]);

            return response()->json(
                [
                    'ok' => false,
                    'message' => 'Error connecting to booking service.',
                ],
                500,
            );
        }
    }

    public function customerSearch(Request $request)
    {
        $q = $request->validate(['q' => 'required|string|min:2'])['q'];

        $hits = User::where(function ($w) use ($q) {
            $w->where('f_name', 'like', "%{$q}%")
                ->orWhere('l_name', 'like', "%{$q}%")
                ->orWhere(DB::raw("CONCAT(f_name, ' ', l_name)"), 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%");
        })
            ->limit(15)
            ->get();

        return response()->json(['ok' => true, 'hits' => $hits]);
    }

    public function customerCreate(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'streetadd' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'zip' => ['required', 'digits_between:3,10'],
        ]);

        [$first, $last] = $this->splitName($data['name']);

        $id = User::insertGetId([
            'f_name' => $first,
            'l_name' => $last,
            'password' => 'default123',
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'street_address' => $data['streetadd'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'zip' => $data['zip'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'data' => [
                'id' => $id,
                'f_name' => $first,
                'l_name' => $last,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'street_address' => $data['streetadd'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'zip' => $data['zip'] ?? null,
            ],
        ]);
    }

    private function splitName(string $full): array
    {
        $full = trim(preg_replace('/\s+/', ' ', str_replace(',', '', $full)));
        if ($full === '') {
            return ['', null];
        }

        $parts = preg_split('/\s+/', $full);

        // Strip common prefixes
        $prefixes = ['mr', 'mrs', 'ms', 'miss', 'dr', 'sir', 'madam', 'mx', 'prof', 'rev'];
        while ($parts && in_array(mb_strtolower(rtrim($parts[0], '.')), $prefixes, true)) {
            array_shift($parts);
        }

        $suffixes = ['jr', 'sr', 'ii', 'iii', 'iv', 'v', 'phd', 'md', 'dds', 'esq'];
        while ($parts && in_array(mb_strtolower(rtrim(end($parts), '.')), $suffixes, true)) {
            array_pop($parts);
        }

        if (count($parts) === 0) {
            return ['', null];
        }
        if (count($parts) === 1) {
            return [$parts[0], null];
        }

        $first = array_shift($parts);
        $last = implode(' ', $parts);
        return [$first, $last];
    }

    public function applyCoupon(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:64'],
            'customer_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $code = trim($data['code']);
        $customerId = isset($data['customer_id']) ? (int) $data['customer_id'] : null;

        $today = now()->toDateString();

        $coupon = DB::table('coupons')
            ->whereRaw('LOWER(`code`) = ?', [mb_strtolower($code)])
            ->where('status', 1)
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('expire_date')->orWhere('expire_date', '>=', $today);
            })
            ->first();

        if (!$coupon) {
            return response()->json(['ok' => false, 'message' => 'Invalid or expired coupon.'], 422);
        }

        if (!empty($coupon->customer_id) && $customerId && (int) $coupon->customer_id !== $customerId) {
            return response()->json(['ok' => false, 'message' => 'Coupon is restricted to a different customer.'], 422);
        }

        $subtotal = 0.0;
        $tax = 0.0;
        $rows = collect();
        $cartId = null;

        if ($customerId) {
            $cartId = DB::table('cart_reservations')->where('customernumber', (string) $customerId)->select('cartid', DB::raw('MAX(updated_at) as last_updated'))->groupBy('cartid')->orderByDesc('last_updated')->value('cartid');

            if ($cartId) {
                $rows = DB::table('cart_reservations')->where('customernumber', (string) $customerId)->where('cartid', $cartId)->lockForUpdate()->get();

                $subtotal = (float) round($rows->sum('subtotal'), 2);
                $tax = (float) round($rows->sum('totaltax'), 2);
            }
        }

        if (!$cartId) {
            $cart = session('admin_res_cart', []);
            $subtotal = (float) round(collect($cart)->sum('price_breakdown.subtotal') ?? 0, 2);
            $tax = (float) round(collect($cart)->sum('price_breakdown.tax') ?? 0, 2);
        }

        if ($subtotal <= 0) {
            return response()->json(['ok' => false, 'message' => 'Cart subtotal is zero.'], 422);
        }

        $minPurchase = (float) $coupon->min_purchase;
        if ($minPurchase > 0 && $subtotal < $minPurchase) {
            return response()->json(['ok' => false, 'message' => 'Minimum purchase not met for this coupon.'], 422);
        }

        if (!is_null($coupon->limit)) {
            $used = DB::table('reservations')->where('discountcode', $coupon->code)->count();
            if ($used >= (int) $coupon->limit) {
                return response()->json(['ok' => false, 'message' => 'Coupon redemption limit has been reached.'], 422);
            }
        }

        $discountType = strtolower((string) $coupon->discount_type);
        $rawDiscount = (float) $coupon->discount;
        $maxDiscount = (float) $coupon->max_discount;

        $discountAmount = 0.0;
        if (in_array($discountType, ['percentage', 'percent'], true)) {
            $discountAmount = round($subtotal * ($rawDiscount / 100), 2);
        } else {
            $discountAmount = round(min($rawDiscount, $subtotal), 2);
        }

        if ($maxDiscount > 0) {
            $discountAmount = min($discountAmount, $maxDiscount);
        }

        $effectiveTaxRate = $subtotal > 0 ? $tax / $subtotal : 0.0;
        $newTax = round(max(0, $subtotal - $discountAmount) * $effectiveTaxRate, 2);
        $newTotal = round($subtotal - $discountAmount + $newTax, 2);

        if ($cartId && $rows->count() > 0) {
            $now = now();

            $remaining = $discountAmount;
            $rowCount = $rows->count();

            foreach ($rows as $index => $line) {
                $lineSubtotal = (float) $line->subtotal;
                $lineRate = (float) $line->taxrate;
                if ($lineSubtotal <= 0) {
                    DB::table('cart_reservations')
                        ->where('id', $line->id)
                        ->update([
                            'discountcode' => $coupon->code,
                            'updated_at' => $now,
                        ]);
                    continue;
                }

                $lineShare = $index === $rowCount - 1 ? $remaining : round($discountAmount * ($lineSubtotal / $subtotal), 2);

                $lineShare = min($lineShare, $remaining, $lineSubtotal);
                $remaining = round($remaining - $lineShare, 2);

                $lineNewTax = round(max(0, $lineSubtotal - $lineShare) * $lineRate, 2);
                $lineNewTotal = round($lineSubtotal - $lineShare + $lineNewTax, 2);

                DB::table('cart_reservations')
                    ->where('id', $line->id)
                    ->update([
                        'discount' => $lineShare,
                        'discountcode' => $coupon->code,
                        'totaltax' => $lineNewTax,
                        'total' => $lineNewTotal,
                        'updated_at' => $now,
                    ]);
            }

            // Re-pull persisted totals to return the authoritative numbers
            $persisted = DB::table('cart_reservations')->where('customernumber', (string) $customerId)->where('cartid', $cartId)->get();

            $subtotal = (float) round($persisted->sum('subtotal'), 2);
            $tax = (float) round($persisted->sum('totaltax'), 2);
            $discountAmount = (float) round($persisted->sum('discount'), 2);
            $newTotal = (float) round($persisted->sum('total'), 2);
            $newTax = $tax; // already recalculated per-row above
        }

        return response()->json([
            'ok' => true,
            'code' => $coupon->code,
            'discounts' => [
                [
                    'label' => $coupon->title ?: 'Coupon',
                    'amount' => $discountAmount,
                    'type' => $coupon->discount_type,
                ],
            ],
            'totals' => [
                'subtotal' => $subtotal,
                'discounts' => $discountAmount,
                'tax' => $newTax,
                'total' => $newTotal,
            ],
            'meta' => [
                'cartid' => $cartId,
                'discountRate' => $discountType, // informational
            ],
        ]);
    }

    // Gift Cart Lookup
    public function giftCardLookup(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:64'],
        ]);

        $card = DB::table('gift_cards')->where('barcode', $data['code'])->first();

        if (!$card) {
            return response()->json(['ok' => false, 'message' => 'Gift card not found.'], 404);
        }
        if (isset($card->status) && !$card->status) {
            return response()->json(['ok' => false, 'message' => 'Gift card is inactive.'], 422);
        }

        $balance = (float) ($card->amount ?? 0);

        return response()->json([
            'ok' => true,
            'code' => $data['code'],
            'balance' => $balance,
        ]);
    }
}
