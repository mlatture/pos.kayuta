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

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Services\CardKnoxService;

class ReservationManagementController extends Controller
{
    public function index()
    {
        $siteTypes = Site::select('id', 'sitename')->orderBy('sitename')->get();
        return view('reservations.management.index', compact('siteTypes'));
    }

    public function availability(Request $request)
    {
        $data = $request->validate([
            'checkin' => ['required', 'date', 'before:checkout'],
            'checkout' => ['required', 'date', 'after:checkin'],
            'rig_length' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'site_id' => ['nullable', 'integer', 'exists:sites,id'],
            'include_offline' => ['sometimes', 'boolean'],
        ]);

        $includeOffline = (bool) ($data['include_offline'] ?? false);

        $query = Site::query()
            ->with(['siteClass:id,siteclass,showriglength,showhookup,showrigtype,tax,orderby', 'siteHookup:id,sitehookup,orderby'])
            ->when($data['site_id'] ?? null, fn($q, $siteId) => $q->where('id', $siteId))
            ->when(!$includeOffline, fn($q) => $q->where('availableonline', 1))
            ->orderBy('orderby');

        if (empty($data['site_id'])) {
            $query->limit(50);
        }

        $sites = $query->get(['id', 'sitename', 'siteclass', 'hookup', 'availableonline', 'maxlength', 'ratetier', 'tax', 'tax_type_id', 'orderby']);

        $checkin = Carbon::parse($data['checkin']);
        $checkout = Carbon::parse($data['checkout']);
        $nights = max(1, $checkin->diffInDays($checkout));
        $rigLen = $data['rig_length'] ?? null;

        $items = $sites
            ->map(function (Site $site) use ($rigLen, $nights) {
                $fits = isset($rigLen) ? is_null($site->maxlength) || (int) $site->maxlength >= (int) $rigLen : true;

                $nightly = 0.0;
                if (!empty($site->ratetier)) {
                    $tier = RateTier::where('tier', $site->ratetier)->first();
                    if ($tier) {
                        $nightly = (float) ($tier->flatrate ?? 0);
                    }
                }
                $getTax = TaxType::find($site->tax_type_id);

                $raw = $getTax->tax ?? 0;


                $taxPercent = (float) preg_replace('/[^0-9.\-]/', '', (string) $raw);

                $taxRate = $taxPercent > 1 ? $taxPercent / 100.0 : $taxPercent;

                $taxRate = max(0.0, min(1.0, $taxRate));

                $subtotal = round(($nightly ?? 0) * $nights, 2);

                $discount = $discount ?? 0.0;
                $taxableBase = max(0.0, $subtotal - $discount);

                $taxAmt = round($taxableBase * $taxRate, 2);
                $total = round($taxableBase + $taxAmt, 2);

                $rawType = (string) $site->siteclass;
                $typeDisplay = preg_replace('/_+/', ' ', $rawType);

                $isRv = (bool) optional($site->siteClass)->showriglength || stripos($rawType, 'rv') !== false;

                $fits = $isRv ? (isset($rigLen) ? is_null($site->maxlength) || (int) $site->maxlength >= (int) $rigLen : true) : false;


                return [
                    'id' => (int) $site->id,
                    'name' => $site->sitename,
                    'type' => $site->siteclass,
                    'type_display' => $typeDisplay,
                    'is_rv' => $isRv,
                    'hookup' => optional($site->siteHookup)->sitehookup,
                    'available_online' => (bool) $site->availableonline,
                    'fits' => $fits,
                    'pricing' => [
                        'nightly' => $nightly,
                        'nights' => $nights,
                        'subtotal' => $subtotal,
                        'tax' => $taxAmt,
                        'total' => $total,
                    ],
                ];
            })
            ->values();

        return response()->json(['ok' => true, 'items' => $items]);
    }

    public function addToCart(Request $request)
    {
        $data = $request->validate([
            'site_id' => ['required', 'integer', 'exists:sites,id'],
            'checkin' => ['required', 'date'],
            'checkout' => ['required', 'date', 'after:checkin'],
            'price_breakdown' => ['required', 'array'],
            'customer_id' => ['nullable', 'integer', 'exists:users,id'],
            'cart_token' => ['nullable', 'string', 'max:64'], // <-- add this
        ]);

        $site = Site::findOrFail($data['site_id']);

        $checkin = Carbon::parse($data['checkin'])->startOfDay();
        $checkout = Carbon::parse($data['checkout'])->startOfDay();
        $nights = max(1, $checkin->diffInDays($checkout));

        $pb = $data['price_breakdown'];
        $nightly = (float) ($pb['nightly'] ?? 0);
        $subtotal = (float) ($pb['subtotal'] ?? $nightly * $nights);
        $taxAmt = (float) ($pb['tax'] ?? 0);
        $discounts = (float) ($pb['discounts'] ?? 0);
        $total = (float) ($pb['total'] ?? max(0, $subtotal - $discounts + $taxAmt));

        $taxable = max(0.0, $subtotal - $discounts);
        $taxRate = $taxable > 0 ? round($taxAmt / $taxable, 4) : 0.0;

        $cartToken = $data['cart_token'] ?? null;
        if (!$cartToken) {
            do {
                $cartToken = 'CN' . mt_rand(100000, 999999);
            } while (CartReservation::where('cartid', $cartToken)->exists());
        }

        $customerId = $data['customer_id'] ?? null;
        $customer = $customerId ? User::find($customerId) : null;

        CartReservation::create([
            'cid' => $checkin,
            'cod' => $checkout,
            'customernumber' => $customerId ? (string) $customerId : null,
            'email' => $customer->email ?? null,
            'hookups' => $site->hookup,
            'cartid' => $cartToken,
            'siteid' => (string) $site->siteid,
            'base' => $nightly,
            'rateadjustment' => 0.0,
            'extracharge' => 0.0,
            'riglength' => (int) $request->input('rig_length', 0) ?: null,
            'sitelock' => '0',
            'nights' => $nights,
            'siteclass' => $site->siteclass,
            'taxrate' => $taxRate,
            'totaltax' => $taxAmt,
            'description' => 'Admin cart add',
            'events' => null,
            'subtotal' => $subtotal,
            'total' => $total,
            'rid' => null,
            'discountcode' => null,
            'discount' => $discounts,
            'holduntil' => now()->addMinutes(15),
            'number_of_guests' => (int) $request->input('number_of_guests', 0) ?: null,
            'addon_id' => null,
            'base_price' => null,
            'product_id' => null,
        ]);

        $count = CartReservation::where('cartid', $cartToken)->count();

        return response()->json([
            'ok' => true,
            'count' => $count,
            'cart_token' => $cartToken,
        ]);
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
