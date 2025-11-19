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

use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Services\CardKnoxService;

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

            if (isset($data['response']['results']['units'])) {
                $units = collect($data['response']['results']['units']);

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
                $includeReserved = $validated['include_reserved'] ?? false;

                if (!$includeReserved) {
                    $units = $units
                        ->filter(function ($unit) {
                            $isReserved = isset($unit['status']['reserved']) ? (bool) $unit['status']['reserved'] : false;
                            return !$isReserved;
                        })
                        ->values();

                    Log::info('Filtered availability to exclude reserved sites', [
                        'remaining_units' => $units->count(),
                    ]);
                }

                //  Update the response
                $data['response']['results']['units'] = $units->values()->all();
                $data['response']['results']['total_units'] = $units->count();
            }

            return response()->json(['data' => $data, 'site_lock_fee' => $siteLockFee]);
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

    public function viewMap(Request $request)
    {
        $result = $this->availability($request);
        $data = $result->getData(true);
        $sites = $data['data']['response']['results']['units'] ?? [];

        $statuses = array_map(fn($sites) => $sites['status'] ?? [], $sites);

        $bookType = $request->book_type ?? 'book_now';
        $grids = Setting::where('key', 'is_grid_view')->value('is_grid_view');

        if ($bookType === 'book_now') {
            // if ($grids == '1') {
            //     return view('reservations.management.map.grid_booking', [
            //         'sites' => $sites['units'] ?? [],
            //         'hookup' => $sites['hookup'] ?? null,
            //         'riglength' => $sites['rig_length'] ?? null,
            //         'siteclass' => $sites['siteclass'] ?? null,
            //     ]);
            // }

            return view('reservations.management.map.booking', [
                'sites' => $sites['units'] ?? [],
                'hookup' => $sites['hookup'] ?? null,
                'riglength' => $sites['rig_length'] ?? null,
                'siteclass' => $sites['siteclass'] ?? null,
                'status' => $statuses,
            ]);
        }

        return view('reservations.management.map.flexible', [
            'sites' => $sites['units'] ?? [],
            'hookup' => $sites['hookup'] ?? null,
            'riglength' => $sites['rig_length'] ?? null,
            'siteclass' => $sites['siteclass'] ?? null,
            'stay' => $sites['stay'] ?? null,
            'months' => $sites['months'] ?? [],
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
        $data = $request->validate([
            'cart_id' => ['required', 'integer'],
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

            if ($response->successful()) {
                Log::info('Added item to cart via booking API', $response->json());
                return response()->json($response->json(), 200);
            }

            Log::error('Cart items API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json(
                [
                    'ok' => false,
                    'message' => 'Failed to add items to cart.',
                    'status' => $response->status(),
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
        Log::info('Checkout started', [
            'request_payload' => $request->all(),
        ]);

        $data = $request->validate([
            'payment_method' => ['required', Rule::in(['cash', 'ach', 'gift_card', 'credit_card'])],
            'gift_card_code' => ['nullable', 'string', 'max:64'],

            'cc.xCardNum' => ['required_if:payment_method,credit_card', 'string', 'max:19'],
            'cc.xExp' => ['required_if:payment_method,credit_card', 'string', 'max:7'],
            'cc.cvv' => ['required_if:payment_method,credit_card', 'string', 'max:4'],

            'ach.routing' => ['required_if:payment_method,ach', 'string', 'max:20'],
            'ach.account' => ['required_if:payment_method,ach', 'string', 'max:30'],
            'ach.name' => ['required_if:payment_method,ach', 'string', 'max:100'],

            'applicable_coupon' => ['nullable', 'string', 'max:50'],

            'custId' => ['nullable', 'integer'],
            'fname' => ['required', 'string', 'max:100'],
            'lname' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:20'],
            'street_address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:50'],
            'zip' => ['required', 'string', 'max:10'],

            'xAmount' => ['required', 'numeric', 'min:0.5'],
            'api_cart.cart_id' => ['required', 'string'],
            'api_cart.cart_token' => ['required', 'string'],
        ]);

        try {
            if (!empty($data['custId'])) {
                Log::info('Updating customer', ['customer_id' => $data['custId']]);

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
                } else {
                    Log::warning('Customer not found for update', [
                        'customer_id' => $data['custId'],
                    ]);
                }
            }

            if (($data['payment_method'] ?? null) === 'credit_card') {
                $data['xCardNum'] = $data['cc']['xCardNum'] ?? null;
                $data['xExp'] = $data['cc']['xExp'] ?? null;
                $data['cvv'] = $data['cc']['cvv'] ?? null;

                unset($data['cc']);
            }

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('BOOKING_BEARER_KEY'),
            ])->post(env('BOOK_API_URL') . 'v1/checkout', $data);

            Log::info('Booking API response', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Checkout failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

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
