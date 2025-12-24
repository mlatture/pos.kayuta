<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\RateTier;
use App\Models\Site;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\AdditionalPayment;
use App\Services\ReservationLogService;
use App\Services\CardKnoxService;

class MoneyActionService
{
    protected $gateway;
    protected $logService;

    public function __construct(ReservationLogService $logService, CardKnoxService $gateway)
    {
        $this->logService = $logService;
        $this->gateway = $gateway;
    }

    /**
     * Add an additional charge to a reservation.
     */
    public function addCharge(Reservation $reservation, float $amount, float $tax, string $comment, string $method = 'cash', ?string $token = null)
    {
        return DB::transaction(function () use ($reservation, $amount, $tax, $comment, $method, $token) {
            $oldState = $reservation->toArray();
            
            $totalAmount = $amount + $tax;
            $gatewayResponse = null;

            if ($method === 'credit_card_on_file' || $method === 'credit_card') {
                if (!$token) {
                    throw new \Exception("Credit card token required for automated charge.");
                }
                
                // Use CardKnoxService to process sale
                $response = $this->gateway->saveSale($token, '000', $totalAmount, $reservation->fname . ' ' . $reservation->lname, $reservation->email);
                
                if (!$response['success']) {
                    throw new \Exception("Gateway Error: " . ($response['message'] ?? 'Unknown error'));
                }
                $gatewayResponse = $response['data'];
            }

            // Create Additional Payment Record
            $payment = AdditionalPayment::create([
                'cartid' => $reservation->cartid,
                'reservation_id' => $reservation->id,
                'method' => $method,
                'amount' => $amount,
                'tax' => $tax,
                'total' => $totalAmount,
                'x_ref_num' => $gatewayResponse['xRefNum'] ?? null,
                'receipt' => 'AC-' . time(),
                'comment' => $comment,
                'created_by' => Auth::user()->name ?? 'System',
            ]);

            // Update Reservation Totals
            $reservation->subtotal += $amount;
            $reservation->totaltax += $tax;
            $reservation->total += $totalAmount;
            $reservation->save();

            $this->logService->log(
                $reservation->id,
                'add_charge',
                $oldState,
                $reservation->refresh()->toArray(),
                "Added charge of \${$amount} + \${$tax} tax via {$method}. Comment: {$comment}. Gateway Ref: " . ($payment->x_ref_num ?? 'N/A')
            );

            return $reservation;
        });
    }

    /**
     * Cancel specific reservations and issue refund.
     */
    public function cancel(Reservation $mainReservation, array $reservationIds, float $refundAmount, float $fee, string $reason, string $method, string $overrideReason = '')
    {
        return DB::transaction(function () use ($mainReservation, $reservationIds, $refundAmount, $fee, $reason, $method, $overrideReason) {
            $reservations = Reservation::whereIn('id', $reservationIds)->get();
            
            $results = [];
            
            if ($method === 'credit_card' && $refundAmount > 0) {
                 // Find original payment reference for the Cart
                $payment = Payment::where('cartid', $mainReservation->cartid)
                    ->whereNotNull('x_ref_num')
                    ->latest()
                    ->first();

                if (!$payment) {
                    throw new \Exception("Original credit card reference (xRefNum) not found for this cart.");
                }

                $gatewayResult = $this->gateway->refund($payment->x_ref_num, $refundAmount, $reason);

                if (($gatewayResult['xStatus'] ?? '') !== 'Approved') {
                    Log::error("Cardknox Refund Failed", ['response' => $gatewayResult, 'cartid' => $mainReservation->cartid]);
                    throw new \Exception("Refund Failed: " . ($gatewayResult['xError'] ?? 'Unknown gateway error'));
                }
                $results['gateway'] = $gatewayResult;
            }

            foreach ($reservations as $res) {
                $oldState = $res->toArray();
                $res->status = 'Cancelled';
                $res->reason = $reason . ($overrideReason ? " (Override: $overrideReason)" : "");
                $res->save();

                Refund::create([
                    'cartid' => $res->cartid,
                    'reservations_id' => $res->id,
                    'amount' => $refundAmount / count($reservations), // Pro-rated or simplified
                    'cancellation_fee' => $fee / count($reservations),
                    'method' => $method,
                    'reason' => $reason,
                    'override_reason' => $overrideReason,
                    'x_ref_num' => $results['gateway']['xRefNum'] ?? null,
                    'created_by' => Auth::user()->name ?? 'System',
                ]);

                $this->logService->log(
                    $res->id,
                    'cancelled',
                    $oldState,
                    $res->refresh()->toArray(),
                    "Cancelled via MoneyAction. Method: $method. Refund: \$$refundAmount. Fee: \$$fee. Reason: $reason. " . 
                    ($overrideReason ? "Override Reason: $overrideReason. " : "") .
                    "Gateway Info: " . json_encode($results['gateway'] ?? 'N/A')
                );
            }

            return $results;
        });
    }

    /**
     * Move a reservation to a different site.
     */
    public function moveSite(Reservation $reservation, string $newSiteId, ?float $overridePrice = null, string $comment = '')
    {
        return DB::transaction(function () use ($reservation, $newSiteId, $overridePrice, $comment) {
            $oldState = $reservation->toArray();
            
            $newSite = Site::where('siteid', $newSiteId)->firstOrFail();
            
            $newPrice = $overridePrice;
            if (is_null($newPrice)) {
                // Simplified price calculation logic based on NewReservationController
                $newPrice = $this->calculatePriceForMove($reservation, $newSite);
            }

            $reservation->siteid = $newSiteId;
            $reservation->siteclass = $newSite->siteclass;
            
            if (!is_null($newPrice)) {
                $diff = $newPrice - $reservation->base;
                $reservation->base = $newPrice;
                // Recalculate tax and total
                $taxRate = $reservation->taxrate ?: 0.0875;
                $reservation->subtotal += $diff;
                $reservation->totaltax = round($reservation->subtotal * $taxRate, 2);
                $reservation->total = $reservation->subtotal + $reservation->totaltax;
            }

            $reservation->save();

            $this->logService->log(
                $reservation->id,
                'move_site',
                $oldState,
                $reservation->refresh()->toArray(),
                "Moved from {$oldState['siteid']} to {$newSiteId}. Comment: $comment" . (is_null($overridePrice) ? "" : " (Price Override Applied)")
            );

            return $reservation;
        });
    }

    /**
     * Change dates of a reservation.
     */
    public function changeDates(Reservation $reservation, string $cid, string $cod, ?float $overridePrice = null, string $comment = '')
    {
        return DB::transaction(function () use ($reservation, $cid, $cod, $overridePrice, $comment) {
            $oldState = $reservation->toArray();
            
            $newCid = Carbon::parse($cid);
            $newCod = Carbon::parse($cod);
            $nights = $newCod->diffInDays($newCid);

            if ($nights <= 0) {
                throw new \Exception("Invalid duration: Checkout must be after Check-in.");
            }

            $reservation->cid = $newCid;
            $reservation->cod = $newCod;
            $reservation->nights = $nights;

            $newPrice = $overridePrice;
            if (is_null($newPrice)) {
                $newPrice = $this->calculatePriceForDates($reservation, $newCid, $newCod);
            }

            if (!is_null($newPrice)) {
                $diff = $newPrice - $reservation->base;
                $reservation->base = $newPrice;
                $taxRate = $reservation->taxrate ?: 0.0875;
                $reservation->subtotal += $diff;
                $reservation->totaltax = round($reservation->subtotal * $taxRate, 2);
                $reservation->total = $reservation->subtotal + $reservation->totaltax;
            }

            $reservation->save();

            $this->logService->log(
                $reservation->id,
                'change_dates',
                $oldState,
                $reservation->refresh()->toArray(),
                "Dates changed from {$oldState['cid']} - {$oldState['cod']} to $cid - $cod. Comment: $comment" . (is_null($overridePrice) ? "" : " (Price Override Applied)")
            );

            return $reservation;
        });
    }

    protected function processCardknoxRefund(Reservation $reservation, float $amount, string $reason)
    {
        $payment = Payment::where('cartid', $reservation->cartid)->whereNotNull('x_ref_num')->latest()->first();

        if (!$payment) {
            throw new \Exception("Original credit card transaction reference (xRefNum) not found.");
        }

        $payload = [
            'xKey' => config('services.cardknox.api_key'),
            'xVersion' => '5.0.0',
            'xSoftwareName' => 'KayutaLake',
            'xSoftwareVersion' => '1.0',
            'xCommand' => 'cc:refund',
            'xRefNum' => $payment->x_ref_num,
            'xAmount' => round($amount, 2),
            'xAllowDuplicate' => 'true',
        ];

        $response = Http::asForm()->post('https://x1.cardknox.com/gateway', $payload);
        parse_str($response->body(), $responseArray);

        if (($responseArray['xStatus'] ?? '') !== 'Approved') {
            Log::error("Cardknox Refund Failed", ['response' => $responseArray, 'cartid' => $reservation->cartid]);
            throw new \Exception('Payment Gateway Refund Failed: ' . ($responseArray['xError'] ?? 'Unknown error'));
        }

        return $responseArray;
    }

    protected function calculatePriceForMove(Reservation $reservation, Site $newSite)
    {
        $rateTier = RateTier::where('tier', $newSite->ratetier)->first();
        if (!$rateTier) return null;

        $nights = $reservation->nights;
        return $this->applyNightsPricing($rateTier, $nights);
    }

    protected function calculatePriceForDates(Reservation $reservation, Carbon $cid, Carbon $cod)
    {
        $site = Site::where('siteid', $reservation->siteid)->first();
        if (!$site) return null;

        $rateTier = RateTier::where('tier', $site->ratetier)->first();
        if (!$rateTier) return null;

        $nights = $cod->diffInDays($cid);
        return $this->applyNightsPricing($rateTier, $nights);
    }

    protected function applyNightsPricing($rateTier, $nights)
    {
        if ($nights < 7) {
            return $rateTier->flatrate * $nights;
        } elseif ($nights == 7) {
            return $rateTier->weeklyrate;
        } else {
            $extraNights = $nights - 7;
            return $rateTier->weeklyrate + ($extraNights * $rateTier->flatrate);
        }
    }
}
