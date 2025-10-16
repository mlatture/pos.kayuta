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
            'pets_ok' => ['sometimes', 'boolean'],
            'include_offline' => ['sometimes', 'boolean'],
            'riglength' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'with_prices' => ['sometimes', 'boolean'],
        ]);

        // Always include with_prices
        $validated['with_prices'] = true;

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

            if ($response->successful()) {
                return response()->json($response->json());
            }

            Log::error('Availability API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json(
                [
                    'ok' => false,
                    'message' => 'Failed to fetch availability data.',
                    'status' => $response->status(),
                ],
                $response->status(),
            );
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

    public function cart(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('BOOKING_BEARER_KEY'),
            ])->post(env('BOOK_API_URL') . 'v1/cart', [
                'utm_source' => 'admin_panel',
                'utm_medium' => 'internal',
                'utm_campaign' => 'reservation_management',
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
        ]);

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('BOOKING_BEARER_KEY'),
            ])->post(env('BOOK_API_URL') . 'v1/cart/items', $data);

            if ($response->successful()) {
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

    public function customerSearch(Request $request)
    {
        $q = $request->validate(['q' => 'required|string|min:2'])['q'];

        $hits = User::where(function ($w) use ($q) {
            $w->where('f_name', 'like', "%{$q}%")
                ->orWhere('l_name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%");
        })
            ->limit(15)
            ->get(['id', 'f_name', 'l_name', 'email', 'phone']);

        return response()->json(['ok' => true, 'hits' => $hits]);
    }

    public function customerCreate(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        [$first, $last] = $this->splitName($data['name']);

        $id = User::insertGetId([
            'f_name' => $first,
            'l_name' => $last,
            'password' => 'default123',
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['ok' => true, 'id' => $id]);
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
    public function checkout(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:users,id'],
            'payment_method' => ['required', Rule::in(['cash', 'ach', 'gift_card', 'credit_card'])],
            'gift_card_code' => ['nullable', 'string', 'max:64'],

            // CC fields required only when payment_method=credit_card
            'cc.number' => ['required_if:payment_method,credit_card', 'string', 'max:19'],
            'cc.exp' => ['required_if:payment_method,credit_card', 'string', 'max:7'], // MM/YY or MMYYYY
            'cc.cvv' => ['required_if:payment_method,credit_card', 'string', 'max:4'],

            // ACH fields required only when payment_method=ach
            'ach.routing' => ['required_if:payment_method,ach', 'string', 'max:20'],
            'ach.account' => ['required_if:payment_method,ach', 'string', 'max:30'],
            'ach.name' => ['required_if:payment_method,ach', 'string', 'max:100'],
        ]);

        $customerId = (int) $data['customer_id'];
        $method = $data['payment_method'];

        $cartId = CartReservation::where('customernumber', (string) $customerId)->select('cartid', DB::raw('MAX(updated_at) as last_updated'))->groupBy('cartid')->orderByDesc('last_updated')->value('cartid');

        if (!$cartId) {
            return response()->json(['ok' => false, 'message' => 'Cart is empty.'], 422);
        }

        DB::beginTransaction();
        try {
            $rows = CartReservation::where('customernumber', (string) $customerId)->where('cartid', $cartId)->orderBy('id')->lockForUpdate()->get();

            if ($rows->isEmpty()) {
                DB::rollBack();
                return response()->json(['ok' => false, 'message' => 'Cart is empty.'], 422);
            }

            $totals = [
                'subtotal' => round((float) $rows->sum('subtotal'), 2),
                'discounts' => round((float) $rows->sum('discount'), 2),
                'tax' => round((float) $rows->sum('totaltax'), 2),
                'total' => round((float) $rows->sum('total'), 2),
            ];

            if ($totals['total'] <= 0) {
                DB::rollBack();
                return response()->json(['ok' => false, 'message' => 'Nothing to charge.'], 422);
            }

            $adminId = (int) auth()->id();
            $orgId = optional(auth()->user())->organization_id;
            $customer = User::findOrFail($customerId);
            $now = now();

            $xRef = null;
            $result = ['xResult' => 'A'];

            if ($method === 'credit_card' || $method === 'ach') {
                /** @var CardKnoxService $cx */
                $cx = app(CardKnoxService::class);
                $name = trim(($customer->f_name ?? '') . ' ' . ($customer->l_name ?? ''));
                $email = $customer->email ?? '';

                if ($method === 'credit_card') {
                    $cardNum = $data['cc']['number'];
                    $exp = $data['cc']['exp'];
                    $cvv = $data['cc']['cvv'];

                    $resp = $cx->sale($cardNum, $cvv, $exp, (float) $totals['total'], $name, $email);
                    $result = $resp;
                    $xRef = $resp['xRefNum'] ?? null;

                    if (($resp['xResult'] ?? null) !== 'A') {
                        DB::rollBack();
                        return response()->json(['ok' => false, 'message' => $resp['xError'] ?? 'Card was declined.'], 422);
                    }
                } elseif ($method === 'ach') {
                    $routing = $data['ach']['routing'];
                    $account = $data['ach']['account'];
                    $accName = $data['ach']['name'];

                    $resp = $cx->achSale($routing, $account, $accName, (float) $totals['total'], $name, $email);
                    $result = $resp;
                    $xRef = $resp['xRefNum'] ?? null;

                    if (($resp['xResult'] ?? null) !== 'A') {
                        DB::rollBack();
                        return response()->json(['ok' => false, 'message' => $resp['xError'] ?? 'ACH was declined.'], 422);
                    }
                }
            } elseif ($method === 'gift_card') {
                $giftCardCode = trim($data['gift_card_code'] ?? '');
                if ($giftCardCode === '') {
                    DB::rollBack();
                    return response()->json(['ok' => false, 'message' => 'Gift card code is required for gift card payments.'], 422);
                }

                $amountToCharge = round((float) $totals['total'], 2);

                $card = GiftCard::where('barcode', $giftCardCode)->lockForUpdate()->first();

                if (!$card) {
                    DB::rollBack();
                    return response()->json(['ok' => false, 'message' => 'Gift card not found.'], 404);
                }

                if ((int) $card->status !== 1) {
                    DB::rollBack();
                    return response()->json(['ok' => false, 'message' => 'Gift card is inactive.'], 422);
                }

                if ((float) $card->amount < $amountToCharge) {
                    DB::rollBack();
                    return response()->json(['ok' => false, 'message' => 'Gift card balance is insufficient.'], 422);
                }

                $card->amount = round(((float) $card->amount) - $amountToCharge, 2);
                $card->save();

                $xRef = 'GC-' . $giftCardCode;
            }

            $reservationIds = $this->createReservationsFromCartRows($rows, $customerId, $adminId, $now);

            CartReservation::where('cartid', $cartId)->update([
                'rid' => (string) ($reservationIds[0] ?? ''),
                'email' => $customer->email ?? null,
                'updated_at' => $now,
            ]);

            $receipt = Receipt::create([
                'cartid' => $cartId,
                'createdate' => $now,
            ]);

            $receiptId = Receipt::where('cartid', $cartId)->value('id');

            $paymentId = DB::table('payments')->insertGetId([
                'amount' => $totals['total'],
                'organization_id' => $orgId,
                'cartid' => $cartId,
                'receipt' => $receiptId,
                'method' => $method,
                'customernumber' => (string) $customerId,
                'email' => $customer->email ?? null,
                'payment' => $totals['total'],
                'x_ref_num' => $xRef,
                'transaction_type' => 'sale',
                'cancellation_fee' => null,
                'refunded_amount' => null,
                'order_id' => $reservationIds[0] ?? null,
                'user_id' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            CartReservation::where('cartid', $cartId)->delete();

            DB::commit();

            return response()->json([
                'ok' => true,
                'message' => 'Reservation created & paid.',
                'payment_id' => $paymentId,
                'reservation_ids' => $reservationIds,
                'cartid' => $cartId,
                'gateway_ref' => $xRef,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Admin checkout failed', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['ok' => false, 'message' => 'Payment or reservation failed. Please retry.'], 422);
        }
    }
    private function createReservationsFromCartRows(\Illuminate\Support\Collection $rows, int $customerId, int $adminId, \Carbon\Carbon $now): array
    {
        $ids = [];
        foreach ($rows as $line) {
            $payload = [
                'siteid' => $line->siteid,
                'siteclass' => $line->siteclass ?? 'Unclassified',
                'cartid' => $line->cartid,
                'customernumber' => (string) $customerId,
                'cid' => optional($line->cid)->toDateString(),
                'cod' => optional($line->cod)->toDateString(),
                'nights' => (int) $line->nights,
                'riglength' => $line->riglength ?? null,
                'taxrate' => $line->taxrate ?? null,
                'subtotal' => (float) $line->subtotal,
                'totaltax' => (float) $line->totaltax,
                'discount' => (float) $line->discount,
                'total' => (float) $line->total,
                'source' => 'Office / Walk-in',
                'email' => $line->email ?? null,
                'fname' => optional($line->user)->f_name ?? 'Guest',
                'lname' => optional($line->user)->l_name ?? '',
                'status' => 'confirmed',
                'createdby' => 'Admin   ',
                'created_at' => $now,
                'updated_at' => $now,
                'createdate' => $now,
                'xconfnum' => $line->cartid,
            ];

            $ids[] = DB::table('reservations')->insertGetId($payload);
        }
        return $ids;
    }
    private function readCart(): array
    {
        return session('admin_res_cart', []);
    }

    private function calcTotals(array $cart): array
    {
        $subtotal = 0.0;
        $tax = 0.0;
        $discounts = 0.0;
        $total = 0.0;
        foreach ($cart as $line) {
            $subtotal += (float) ($line['price_breakdown']['subtotal'] ?? 0);
            $tax += (float) ($line['price_breakdown']['tax'] ?? 0);
            $discounts += (float) ($line['price_breakdown']['discounts'] ?? 0);
            $total += (float) ($line['price_breakdown']['total'] ?? 0);
        }
        return [
            'subtotal' => round($subtotal, 4),
            'tax' => round($tax, 4),
            'discounts' => round($discounts, 4),
            'total' => round($total, 4),
        ];
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
