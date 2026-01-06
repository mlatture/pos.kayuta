<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Site;
use App\Models\SiteClass;
use App\Models\SiteHookup;
use App\Models\RateTier;
use App\Models\BusinessSettings;
use App\Models\ReservationDraft;
use App\Models\Addon;
use App\Models\Coupon;
use App\Models\User; // Added
use App\Models\CartReservation; // Added
use App\Models\Reservation; // Added
use App\Models\Payment; // Added
use App\Services\ReservationLogService; // Added
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // Added
use Illuminate\Support\Str;
use Carbon\Carbon;

class FlowReservationController extends Controller
{
    public function step1(Request $request)
    {
        $siteClasses = SiteClass::orderBy('siteclass')->get();
        $siteHookups = SiteHookup::orderBy('orderby')->get();
        $addons = Addon::all();
        
        return view('flow-reservation.step1', compact('siteClasses', 'siteHookups', 'addons'));
    }

    public function saveDraft(Request $request)
    {
        $request->validate([
            'cart_data' => 'required|array',
            'totals' => 'required|array',
        ]);

        $draftId = (string) Str::uuid();
        
        $draft = ReservationDraft::create([
            'draft_id' => $draftId,
            'cart_data' => $request->cart_data,
            'subtotal' => $request->totals['subtotal'] ?? 0,
            'discount_total' => $request->totals['discount_total'] ?? 0,
            'estimated_tax' => $request->totals['estimated_tax'] ?? 0,
            'platform_fee_total' => $request->totals['platform_fee_total'] ?? 0,
            'grand_total' => $request->totals['grand_total'] ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'draft_id' => $draftId,
            'redirect_url' => route('flow-reservation.step2', ['draft_id' => $draftId])
        ]);
    }

    public function step2(Request $request, $draft_id)
    {
        $draft = ReservationDraft::where('draft_id', $draft_id)->firstOrFail();
        
        if ($draft->status === 'confirmed') {
            return redirect()->to('admin/reservations/invoice/' . $draft->draft_id);
        }

        $primaryCustomer = $draft->customer_id ? User::find($draft->customer_id) : null;
        
        return view('flow-reservation.step2', compact('draft', 'primaryCustomer'));
    }

    public function updateCustomer(Request $request, $draft_id)
    {
        $draft = ReservationDraft::where('draft_id', $draft_id)->firstOrFail();
        
        $validated = $request->validate([
            'customer_id' => 'nullable|integer',
            'primary' => 'nullable|array',
            'guest_data' => 'nullable|array',
        ]);

        $customerId = $validated['customer_id'] ?? null;
        $primary = $validated['primary'] ?? [];

        // If creating a new customer
        if (!$customerId && !empty($primary['f_name'])) {
            // Check if user already exists by email if provided
            $user = null;
            if (!empty($primary['email'])) {
                $user = \App\Models\User::where('email', $primary['email'])->first();
            }

            if (!$user) {
                $user = \App\Models\User::create([
                    'f_name' => $primary['f_name'],
                    'l_name' => $primary['l_name'] ?? '',
                    'email' => $primary['email'] ?? null,
                    'phone' => $primary['phone'] ?? null,
                    'street_address' => $primary['street_address'] ?? null,
                    'city' => $primary['city'] ?? null,
                    'state' => $primary['state'] ?? null,
                    'zip' => $primary['zip'] ?? null,
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(12)),
                ]);
            }
            $customerId = $user->id;
        } elseif ($customerId) {
            // Update existing customer
            $user = \App\Models\User::find($customerId);
            if ($user) {
                $user->update([
                    'f_name' => $primary['f_name'] ?? $user->f_name,
                    'l_name' => $primary['l_name'] ?? $user->l_name,
                    'email' => $primary['email'] ?? $user->email,
                    'phone' => $primary['phone'] ?? $user->phone,
                    'street_address' => $primary['street_address'] ?? $user->street_address,
                    'city' => $primary['city'] ?? $user->city,
                    'state' => $primary['state'] ?? $user->state,
                    'zip' => $primary['zip'] ?? $user->zip,
                ]);
            }
        }

        $draft->customer_id = $customerId;
        if ($request->has('guest_data')) {
            $draft->guest_data = $validated['guest_data'];
        }

        $draft->save();

        return response()->json([
            'success' => true,
            'message' => 'Customer information updated successfully.',
            'customer_id' => $customerId
        ]);
    }


    public function removeItem(Request $request, $draft_id)
    {
        $draft = ReservationDraft::where('draft_id', $draft_id)->firstOrFail();
        $index = $request->input('index');
        
        $cart = $draft->cart_data;
        if (isset($cart[$index])) {
            array_splice($cart, $index, 1);
            $draft->cart_data = $cart;
            
            // Recalculate totals
            $subtotal = 0;
            $platformFeeTotal = 0;
            foreach ($cart as $item) {
                $subtotal += ($item['base'] ?? 0) + ($item['fee'] ?? 0);
                foreach ($item['addons'] ?? [] as $addon) {
                    $subtotal += $addon['price'] ?? 0;
                }
                $platformFeeTotal += $item['fee'] ?? 0;
            }

            $discount = $draft->discount_total;
            $subtotalAfterDiscount = max(0, $subtotal - $discount);
            $tax = $subtotalAfterDiscount * 0.07; // Reusing 7% logic
            
            $draft->subtotal = $subtotal;
            $draft->platform_fee_total = $platformFeeTotal;
            $draft->estimated_tax = $tax;
            $draft->grand_total = $subtotalAfterDiscount + $tax;
            
            $draft->save();
        }

        return response()->json([
            'success' => true,
            'draft' => $draft
        ]);
    }


    public function search(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'siteclass' => ['nullable', 'string'],
            'hookup' => ['nullable', 'string'],
            'rig_length' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $query = [
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'with_prices' => true,
            'view' => 'units',
        ];

        if (!empty($validated['siteclass'])) $query['siteclass'] = $validated['siteclass'];
        if (!empty($validated['hookup'])) $query['hookup'] = $validated['hookup'];
        if (!empty($validated['rig_length'])) $query['rig_length'] = $validated['rig_length'];

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('BOOKING_BEARER_KEY'),
            ])->get(env('BOOK_API_URL') . 'v1/availability', $query);

            if (!$response->successful()) {
                return response()->json(['ok' => false, 'message' => 'API Error'], 500);
            }

            $data = $response->json();
            $units = collect($data['response']['results']['units'] ?? [])
                ->filter(fn($u) => isset($u['status']['available']) && $u['status']['available'] === true)
                ->values();

            // Filter by rig length and site class manually if needed (reusing ReservationManagementController logic)
            // For now, let's assume the API handles it well enough or we can add more filters if needed.

            return response()->json([
                'ok' => true,
                'data' => [
                    'response' => [
                        'results' => [
                            'units' => $units
                        ],
                        'view' => 'units'
                    ]
                ],
                'platform_fee' => BusinessSettings::where('type', 'platform_fee')->value('value') ?? 5.00
            ]);

        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function finalize(Request $request, $draft_id)
    {
        $draft = ReservationDraft::where('draft_id', $draft_id)
            ->where('status', 'draft')
            ->firstOrFail();

        if (!$draft->customer_id) {
            return response()->json(['success' => false, 'message' => 'Customer must be bound before finalization.'], 422);
        }

        $customer = User::findOrFail($draft->customer_id);

        try {
            return DB::transaction(function () use ($draft, $request, $customer) {
                $randomReceiptID = rand(1000, 9999);
                $cartId = $draft->draft_id;

                $paymentType = $request->payment_method ?? 'Cash';
                $amountPaid = $request->amount ?? $draft->grand_total;

                $normalizedMethod = $paymentType;
                if ($paymentType === 'GiftCard') $normalizedMethod = 'Gift Card';
                if ($paymentType === 'CreditCard') $normalizedMethod = 'Manual';
                if ($paymentType === 'Check') $normalizedMethod = 'Check';

                $payment = new Payment([
                    'cartid' => $cartId,
                    'receipt' => $randomReceiptID,
                    'method' => $normalizedMethod,
                    'customernumber' => $customer->id,
                    'email' => $customer->email,
                    'payment' => $amountPaid,
                    'x_ref_num' => $request->x_ref_num ?? null,
                    'acc_number' => $request->acc_number ?? null,
                ]);
                $payment->save();

                // 2. Create Reservations
                foreach ($draft->cart_data as $item) {
                    $addons = $item['addons'] ?? [];
                    $addonsJson = json_encode($addons);

                    // Create CartReservation (Detail Line)
                    $cartRes = new CartReservation([
                        'cartid' => $cartId,
                        'siteid' => $item['site_id'],
                        'cid' => $item['start_date'],
                        'cod' => $item['end_date'],
                        'customernumber' => $customer->id,
                        'email' => $customer->email,
                        'total' => $item['totals']['total'],
                        'subtotal' => $item['totals']['subtotal'],
                        'taxrate' => 0.07, 
                        'totaltax' => $item['totals']['tax'],
                        'siteclass' => $item['site_type'] ?? 'RV',
                        'nights' => $item['nights'],
                        'base' => $item['totals']['subtotal'],
                        'sitelock' => $item['totals']['sitelock_fee'] ?? 0,
                        'hookups' => $item['rig_type'] ?? '',
                        'riglength' => $item['rig_length'] ?? 0,
                        'addon_id' => !empty($addons) ? $addons[0]['id'] : null, // Simplified for CartReservation
                    ]);
                    $cartRes->save();

                    // Create Reservation (Main Entry)
                    $reservation = new Reservation([
                        'cartid' => $cartId,
                        'source' => 'Web Flow',
                        'email' => $customer->email,
                        'fname' => $customer->f_name,
                        'lname' => $customer->l_name,
                        'customernumber' => $customer->id,
                        'siteid' => $item['site_id'],
                        'cid' => $item['start_date'],
                        'cod' => $item['end_date'],
                        'total' => $item['totals']['total'],
                        'subtotal' => $item['totals']['subtotal'],
                        'taxrate' => 7,
                        'totaltax' => $item['totals']['tax'],
                        'siteclass' => $item['site_type'] ?? 'RV',
                        'nights' => $item['nights'],
                        'base' => $item['totals']['subtotal'],
                        'sitelock' => $item['totals']['sitelock_fee'] ?? 0,
                        'rigtype' => $item['rig_type'] ?? '',
                        'riglength' => $item['rig_length'] ?? 0,
                        'xconfnum' => $cartId,
                        'createdby' => auth()->user()->name ?? 'system',
                        'receipt' => $randomReceiptID,
                        'rid' => 'uc',
                        'status' => 'Confirmed',
                        'addons_json' => $addonsJson,
                    ]);
                    $reservation->save();

                    if (app()->bound(ReservationLogService::class)) {
                        try {
                            app(ReservationLogService::class)->log(
                                $reservation->id,
                                'created',
                                null,
                                $reservation->toArray(),
                                "Reservation #{$reservation->id} confirmed via Web Flow"
                            );
                        } catch (\Exception $logEx) {
                            Log::warning("Logging failed: " . $logEx->getMessage());
                        }
                    }
                }

                $draft->status = 'confirmed';
                $draft->save();

                return response()->json(['success' => true, 'message' => 'Confirmed', 'order_id' => $cartId]);
            });
        } catch (\Exception $e) {
            Log::error("Finalize Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
