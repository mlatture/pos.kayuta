<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\CartReservation;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\AdditionalPayment;
use App\Services\MoneyActionService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\ReservationLogService;

class MoneyActionController extends Controller
{
    protected $moneyService;

    public function __construct(MoneyActionService $moneyService)
    {
        $this->moneyService = $moneyService;
        $this->middleware('auth');
        // Add permission middleware if available, e.g.:
        // $this->middleware('admin_has_permission:reservation_management');
    }

    public function addCharge(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'tax' => 'required|numeric|min:0',
            'comment' => 'required|string|max:500',
            'method' => 'nullable|string',
            'token' => 'nullable|string',
            'register_id' => 'nullable|string',
        ]);

        try {
            $reservation = Reservation::findOrFail($id);
            $this->moneyService->addCharge(
                $reservation, 
                $request->amount, 
                $request->tax, 
                $request->comment,
                $request->method ?? 'cash',
                $request->token,
                $request->register_id
            );

            return response()->json([
                'success' => true, 
                'message' => 'Charge added successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to add charge: ' . $e->getMessage()
            ], 422);
        }
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reservation_ids' => 'required|array',
            'reservation_ids.*' => 'exists:reservations,id',
            'refund_amount' => 'nullable|numeric|min:0',
            'fee_percent' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
            'method' => 'required|in:credit_card,cash,other,account_credit,gift_card',
            'override_reason' => 'nullable|string|max:500',
            'register_id' => 'nullable|string',
        ]);

        try {
            $mainReservation = Reservation::where('cartid', $id)->firstOrFail();
            $this->moneyService->cancel(
                $mainReservation, 
                $request->reservation_ids, 
                $request->fee_percent, 
                $request->reason, 
                $request->method,
                $request->override_reason ?? '',
                $request->register_id
            );

            return response()->json([
                'success' => true, 
                'message' => 'Reservation(s) cancelled successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Cancellation failed: ' . $e->getMessage()
            ], 422);
        }
    }

    public function moveSite(Request $request, $id)
    {
        $request->validate([
            'new_site_id' => 'required|exists:sites,siteid',
            'override_price' => 'nullable|numeric|min:0',
            'comment' => 'required|string|max:500',
        ]);

        try {
            $reservation = Reservation::findOrFail($id);
            $this->moneyService->moveSite(
                $reservation, 
                $request->new_site_id, 
                $request->override_price, 
                $request->comment
            );

            return response()->json([
                'success' => true, 
                'message' => 'Site moved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Move failed: ' . $e->getMessage()
            ], 422);
        }
    }

    public function moveOptions($id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            $options = $this->moneyService->moveOptions($reservation);
            return response()->json([
                'success' => true,
                'options' => $options
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch move options: ' . $e->getMessage()
            ], 422);
        }
    }

    public function changeDates(Request $request, $id)
    {
        $request->validate([
            'cid' => 'required|date',
            'cod' => 'required|date|after:cid',
            'override_price' => 'nullable|numeric|min:0',
            'comment' => 'required|string|max:500',
        ]);

        try {
            $reservation = Reservation::findOrFail($id);
            $this->moneyService->changeDates(
                $reservation, 
                $request->cid, 
                $request->cod, 
                $request->override_price, 
                $request->comment
            );

            return response()->json([
                'success' => true, 
                'message' => 'Dates changed successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Date change failed: ' . $e->getMessage()
            ], 422);
        }
    }

    public function startModification(Request $request, $id)
    {
        try {
            // 1. Load existing reservation(s) by cartid (some carts have multiple sites)
            $reservations = Reservation::where('cartid', $id)->get();
            if ($reservations->isEmpty()) {
                // Try finding by single ID if cartid not found
                $singleRes = Reservation::find($id);
                if ($singleRes) {
                    $reservations = Reservation::where('cartid', $singleRes->cartid)->get();
                }
            }

            if ($reservations->isEmpty()) {
                return redirect()->back()->with('error', 'Reservation not found.');
            }

            $mainRes = $reservations->first();
            $cartId = $mainRes->cartid;

            // 2. Calculate Net Paid Amount (Credit to be applied)
            // Logic from show.blade.php ledger
            $payments = Payment::where('cartid', $cartId)->get();
            $additionalPayments = AdditionalPayment::where('cartid', $cartId)->get();
            $refunds = Refund::where('cartid', $cartId)->get();

            $totalPaid = $payments->sum('payment') + $additionalPayments->sum('total');
            $totalRefunded = $refunds->sum('amount');
            $creditAmount = $totalPaid - $totalRefunded;

            if ($creditAmount < 0) $creditAmount = 0;

            // 3. Clear existing cart for guest/system
            // Carts are currently linked to customernumber in CartReservation
            CartReservation::where('customernumber', $mainRes->customernumber)->delete();

            // 4. Add Credit Line Item to Cart
            $modCartId = '';
            do {
                $modCartId = 'MOD-' . strtoupper(bin2hex(random_bytes(3)));
            } while (Reservation::where('cartid', $modCartId)->exists());

            CartReservation::create([
                'customernumber' => $mainRes->customernumber,
                'cid' => $mainRes->cid,
                'cod' => $mainRes->cod,
                'cartid' => $modCartId, // Temporary cart ID for the process
                'siteid' => 'CREDIT',
                'description' => "Credit from Reservation #{$cartId}",
                'base' => -$creditAmount,
                'subtotal' => -$creditAmount,
                'total' => -$creditAmount,
                'taxrate' => 0,
                'totaltax' => 0,
                'nights' => 0,
                'rid' => $cartId, // Using rid to store originating cartid for reference at checkout
                'holduntil' => now()->addHours(2),
                'email' => $mainRes->email
            ]);

            // Log modification start
            app(ReservationLogService::class)->log(
                $mainRes->id,
                'modification_started',
                null,
                ['credit_amount' => $creditAmount, 'cart_id' => $cartId],
                "Modification process started. Credit of $".number_format($creditAmount, 2)." applied."
            );

            // 5. Redirect to Search / Availability page with pre-fills
            return redirect()->route('admin.reservation_mgmt.index', [
                'admin' => auth()->id(),
                'cid' => $mainRes->cid->format('Y-m-d'),
                'cod' => $mainRes->cod->format('Y-m-d'),
                'cart_id' => $modCartId
            ])->with('success', 'Modification initiated. Credit of $' . number_format($creditAmount, 2) . ' applied to cart.');

        } catch (\Exception $e) {
            Log::error("Modification Start Failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to start modification: ' . $e->getMessage());
        }
    }
}
