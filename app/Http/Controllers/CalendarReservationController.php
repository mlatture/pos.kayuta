<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Site;
use App\Models\Payment;
use App\Models\SiteClass;
use App\Models\SiteHookup;
use App\Models\Reservation;
use App\Models\RigTypes;
use App\Models\User;
use App\Models\BusinessSettings;
use Illuminate\Http\Request;

class CalendarReservationController extends Controller
{
    public function index($id)
    {
        $reservation = Reservation::where('cartid', $id)->first();
        $getSiteClass = SiteClass::all();
        $getSites = Site::all();
        $getHookup = SiteHookup::all();
        $getPaidAmount = Payment::where('cartid', $id)->first();

        $cid = Carbon::parse($reservation->cid)->format('M d, Y');
        $cod = Carbon::parse($reservation->cod)->format('M d, Y');

        return view('reservations.relocate', [
            'reservation' => $reservation,
            'siteclasses' => $getSiteClass,
            'cid' => $cid,
            'cod' => $cod,
            'sites' => $getSites,
            'hookups' => $getHookup,
            'paidAmount' => $getPaidAmount,
        ]);
    }

    // public function show($id)
    // {
    //     $reservations = Reservation::where('cartid', $id)->with(['payment', 'cart_reservation', 'refunds'])->get();

    //     abort_if($reservations->isEmpty(), 404, 'Reservations not found.');
    //     $rigTypes = RigTypes::all();
    //     $payment = Payment::where('cartid', $id)->get();
    //     $business_settings = BusinessSettings::where('type', 'cancellation')->first();
    //     $cancellation = $business_settings ? json_decode($business_settings->value, true) : [];

    //     $user = User::where('id', $reservations->first()->customernumber)->first();

    //     $allRefunded = $reservations->every(function ($reservation) {
    //         return $reservation->refunds->isNotEmpty();
    //     });

    //     return view('reservations.edit', compact(['reservations', 'rigTypes', 'user', 'payment', 'allRefunded', 'cancellation']));
    // }

    public function show($cartId)
    {
        $reservations = Reservation::where('cartid', $cartId)
            ->with(['payment', 'cart_reservation', 'refunds', 'site', 'user'])
            ->get();

        abort_if($reservations->isEmpty(), 404, 'Reservations not found.');

        $rigTypes = RigTypes::all();

        $payments = $reservations->flatMap(fn($res) => $res->payment);

        $businessSettings = BusinessSettings::where('type', 'cancellation')->first();
        $cancellation = $businessSettings ? json_decode($businessSettings->value, true) : [];

        $user = User::where('id', $reservations->first()->customernumber)->first();

        $allRefunded = $reservations->every(fn($reservation) => $reservation->refunds->isNotEmpty());

        return view('reservations.edit', [
            'reservations' => $reservations,
            'rigTypes' => $rigTypes,
            'user' => $user,
            'payments' => $payments,
            'allRefunded' => $allRefunded,
            'cancellation' => $cancellation,
        ]);
    }

    public function getUnavailableDates($id)
    {
        $reservations = Reservation::get(['cid', 'cod']);

        $unavailable_dates = [];
        foreach ($reservations as $reservation) {
            $unavailable_dates[] = [
                'cid' => date('Y-m-d', strtotime($reservation->cid)),
                'cod' => date('Y-m-d', strtotime($reservation->cod)),
            ];
        }

        return response()->json([
            'unavailable_dates' => $unavailable_dates,
        ]);
    }

    public function filterSites(Request $request)
    {
        $fromDate = Carbon::parse($request->cid);
        $toDate = Carbon::parse($request->cod);

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

    public function updateSitePricing(Request $request)
    {
        $cid = Carbon::parse($request->cid);
        $cod = Carbon::parse($request->cod);
        $siteid = $request->site_id;
        $siteclass = $request->siteclass;
        $hookup = $request->hookup;
        $total = $request->total_amount;
        $cartid = $request->cartid;

        $reservation = Reservation::where('cartid', $cartid)->first();

        $reservation->update([
            'siteid' => $siteid,
            'siteclass' => $siteclass,
            'total' => $total,
            'cid' => $cid,
            'cod' => $cod,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reservation updated successfully',
        ]);
    }
}
