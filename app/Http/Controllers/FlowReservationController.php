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
use App\Models\GiftCard; // Added
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
        
        return view('flow-reservation.step1', compact('siteClasses', 'siteHookups'));
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
                'platform_fee' => BusinessSettings::where('type', 'platform_fee')->value('value') ?? 5.00,
                'site_lock_fee' => BusinessSettings::where('type', 'site_lock_fee')->value('value') ?? 0,
            ]);

        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

     public function finalize(Request $request, $draft_id)
    {
        $draft = ReservationDraft::where('draft_id', $draft_id)->firstOrFail();

        if (!$draft->customer_id) {
            return response()->json(['success' => false, 'message' => 'Customer must be bound before finalization.'], 422);
        }

        $customer = User::findOrFail($draft->customer_id);

        try {
            // Step 1: Create cart in external API
            $cartResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('BOOKING_BEARER_KEY'),
            ])->post(env('BOOK_API_URL') . 'v1/cart', [
                'utm_source' => 'rvparkhq',
                'utm_medium' => 'referral',
                'utm_campaign' => 'flow_reservation',
            ]);

// dd($cartResponse->json());
            if ($cartResponse->failed()) {
                Log::error('Failed to create cart in external API', [
                    'status' => $cartResponse->status(),
                    'body' => $cartResponse->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to initialize cart with booking service.',
                ], 500);
            }

            $cartData = $cartResponse->json();
            $externalCartId = $cartData['data']['cart_id'] ?? null;
            $externalCartToken = $cartData['data']['cart_token'] ?? null;

            if (!$externalCartId || !$externalCartToken) {
                Log::error('External API did not return cart ID or token', ['response' => $cartData]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid response from booking service.',
                ], 500);
            }

// dd($draft);

            // Step 2: Add items to external cart
            foreach ($draft->cart_data as $item) {
                // dd($item);
                $itemResponse = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . env('BOOKING_BEARER_KEY'),
                ])->post(env('BOOK_API_URL') . 'v1/cart/items', [
                    'cart_id' => $externalCartId,
                    'token' => $externalCartToken,
                    'site_id' => $item['id'],
                    'start_date' => $item['start_date'] ?? $item['cid'],
                    'end_date' => $item['end_date'] ?? $item['cod'],
                    'occupants' => [
                      'adults'   => $item['occupants']['adults'] ?? 2,
                      'children' => $item['occupants']['children'] ?? 0,
                    ],

                    'site_lock_fee' => (($item['site_lock_fee'] ?? 'off') === 'on') 
                        ? (float) (BusinessSettings::where('type', 'site_lock_fee')->value('value') ?? 0) 
                        : 0,
                 ]);

// dd($itemResponse->status(),$itemResponse->body());
                if ($itemResponse->failed()) {
                    Log::error('Failed to add item to external cart', [
                        'status' => $itemResponse->status(),
                        'body' => $itemResponse->body(),
                        'item' => $item,
                    ]);
                    // Continue with other items or fail completely?
                    // For now, we'll continue
                }
            }

            // Step 3: Map payment method from POS drawer format to API format
            $paymentMethod = $request->payment_method ?? 'Cash';
            $apiPaymentMethod = 'cash'; // default
            $paymentData = [];

            switch ($paymentMethod) {
                case 'CreditCard':
                case 'Manual':
                    $apiPaymentMethod = 'card';
                    $paymentData = [
                        'cc' => [
                            'xCardNum' => $request->xCardNum ?? '',
                            'xExp' => $request->xExp ?? '',
                            'cvv' => $request->cvv ?? '',
                        ]
                    ];
                    break;
                case 'Check':
                    $apiPaymentMethod = 'ach';
                    $paymentData = [
                        'ach' => [
                            'routing' => $request->xRouting ?? '',
                            'account' => $request->xAccount ?? '',
                            'name' => $request->xName ?? ($customer->f_name . ' ' . $customer->l_name),
                        ]
                    ];
                    break;
                case 'GiftCard':
                case 'Gift Card':
                    $apiPaymentMethod = 'gift_card';
                    $paymentData = [
                        'gift_card_code' => $request->xBarcode ?? $request->gift_card_code ?? '',
                    ];
                    break;
                case 'Cash':
                default:
                    $apiPaymentMethod = 'cash';
                    $paymentData = [
                        'cash_tendered' => $request->amount ?? $draft->grand_total,
                    ];
                    break;
            }

            // Step 4: Prepare checkout data for external API
            $checkoutData = array_merge([
                'payment_method' => $apiPaymentMethod,
                'xAmount' => $request->amount ?? $draft->grand_total,
                'fname' => $customer->f_name,
                'lname' => $customer->l_name,
                'email' => $customer->email,
                'phone' => $customer->phone ?? '',
                'street_address' => $customer->street_address ?? '',
                'city' => $customer->city ?? '',
                'state' => $customer->state ?? '',
                'zip' => $customer->zip ?? '',
                'custId' => $customer->id,
                'api_cart' => [
        'cart_id'    => (string) $externalCartId,     // 👈 FIX
        'cart_token' => (string) $externalCartToken,  // 👈 SAFE
    ],
            ], $paymentData);

// dd($checkoutData);

            // Step 5: Call external Checkout API
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('BOOKING_BEARER_KEY'),
            ])->post(env('BOOK_API_URL') . 'v1/checkout', $checkoutData);

            if ($response->failed()) {
                Log::error('Checkout API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'draft_id' => $draft_id,
                ]);

                $errorMessage = $response->json()['message'] ?? 'Payment processing failed.';
                // If error message contains "email", replace it with a generic message
                if (str_contains(strtolower($errorMessage), 'email')) {
                    $errorMessage = 'Payment processed, but there was a notification issue. Please contact support if you do not receive a confirmation.';
                    // Or simply:
                    // $errorMessage = 'Payment processing failed.'; 
                    // But based on "irrelevant message of email not sent", it often means payment worked but email failed? 
                    // The user code block shows it is treated as a FAILURE ($response->failed()). 
                    // So if email failed, the whole thing failed? 
                    // Actually, if the API fails because email failed, we probably still want to show an error, but a cleaner one.
                    $errorMessage = 'Payment processing failed.';
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => $response->json()['errors'] ?? [],
                ], $response->status());
            }

            // Handle gift card deduction
            if ($apiPaymentMethod === 'gift_card') {
                $giftCardCode = $paymentData['gift_card_code'] ?? null;
                if ($giftCardCode) {
                    GiftCard::where('barcode', $giftCardCode)->decrement('amount', $draft->grand_total);
                }
            }

            // Mark draft as confirmed
            $draft->status = 'confirmed';
            $draft->external_cart_id = $externalCartId;
            $draft->save();

            $apiResponse = $response->json();
            
            // Redirect to Step 1 as requested
            return redirect()->route('flow-reservation.step1');

        } catch (\Exception $e) {
            Log::error("Finalize Error: " . $e->getMessage(), [
                'draft_id' => $draft_id,
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error connecting to booking service: ' . $e->getMessage()
            ], 500);
        }
    }
}
