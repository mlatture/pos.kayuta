<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Site;
use App\Models\Users;
use App\Models\SiteClass;
use App\Models\RateTier;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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
                        // use your real logic here (weekday/seasonal/etc). For now flatrate:
                        $nightly = (float) ($tier->flatrate ?? 0);
                    }
                }

                $subtotal = round($nightly * $nights, 2);
                $taxAmt = round($subtotal * 0.08, 2); // TODO: replace with real tax by tax_type_id
                $total = $subtotal + $taxAmt;

                return [
                    'id' => (int) $site->id,
                    'name' => $site->sitename,
                    'type' => $site->siteclass,
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
            'site_id' => ['required', 'integer'],
            'checkin' => ['required', 'date'],
            'checkout' => ['required', 'date', 'after:checkin'],
            'price_breakdown' => ['required', 'array'],
        ]);

        $cart = session()->get('admin_res_cart', []);
        $cart[] = $data;
        session()->put('admin_res_cart', $cart);

        return response()->json(['ok' => true, 'count' => count($cart)]);
    }

    public function cart()
    {
        $cart = session('admin_res_cart', []);
        return response()->json(['ok' => true, 'cart' => $cart]);
    }

    public function customerSearch(Request $request)
    {
        $q = $request->validate(['q' => 'required|string|min:2'])['q'];

        $hits = DB::table('users')
            ->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            })
            ->limit(15)
            ->get(['id', 'name', 'email', 'phone']);

        return response()->json(['ok' => true, 'hits' => $hits]);
    }

    public function customerCreate(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        // TODO: Use your dedicated Customer create flow if different.
        $id = DB::table('users')->insertGetId([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['ok' => true, 'id' => $id]);
    }

    public function applyCoupon(Request $request)
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:64']]);

        $cart = session('admin_res_cart', []);
        $subtotal = collect($cart)->sum('price_breakdown.subtotal') ?? 0;
        $tax = collect($cart)->sum('price_breakdown.tax') ?? 0;
        $discount = 0.0; // apply real discount here
        $total = $subtotal - $discount + $tax;

        return response()->json([
            'ok' => true,
            'code' => $data['code'],
            'discounts' => [['label' => 'Coupon', 'amount' => $discount]],
            'totals' => [
                'subtotal' => $subtotal,
                'discounts' => $discount,
                'tax' => $tax,
                'total' => $total,
            ],
        ]);
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:users,id'],
            'payment_method' => ['required', Rule::in(['cash', 'ach', 'gift_card', 'credit_card'])],
            'gift_card_code' => ['nullable', 'string', 'max:64'],
            'ach' => ['nullable', 'array'],
            'cc' => ['nullable', 'array'],
        ]);

        DB::beginTransaction();
        try {
            // TODO:
            // - Validate inventory and hold/commit sites
            // - Create reservation(s) aligned to your book-site schema
            // - Process payment via SOLA / Gift card systems
            // - Write transaction logs and audit (admin user, etc.)
            // - Generate receipt

            // Clear session cart (demo)
            session()->forget('admin_res_cart');

            DB::commit();
            return response()->json(['ok' => true, 'message' => 'Reservation created & paid.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Admin checkout failed', ['error' => $e->getMessage()]);
            return response()->json(['ok' => false, 'message' => 'Payment or reservation failed. Please retry.'], 422);
        }
    }
}
