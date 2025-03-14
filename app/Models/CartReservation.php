<?php

namespace App\Models;

use App\Models\RateTier;
use App\Models\Site;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
class CartReservation extends Model
{
    use HasFactory;

    protected $table = 'cart_reservations';
    protected $date = ['cid', 'cod'];

    protected $fillable = [
        'customernumber',
        'cid',
        'cod',
        'cartid',
        'siteid',
        'riglength',
        'sitelock',
        'nights',
        'siteclass',
        'hookups',
        'email',
        'base',
        'subtotal',
        'number_of_guests',
        'taxrate',
        'totaltax',
        'total',
        'rid',
        'holduntil',
        'description',
    ];

    protected $dates = ['holduntil'];
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'customernumber', 'id');
    }

    public static function calculateRate($fromDate, $toDate, $tier, $request)
    {
        $numberOfNights = $toDate->diffInDays($fromDate);
    
        if ($numberOfNights <= 0) {
            throw new Exception('Invalid number of nights');
        }
    
       
        $baseRatePerNight = $tier->flatrate;
    
        $rate = $baseRatePerNight * $numberOfNights;
    
     
        $today = Carbon::today();
        $daysUntilCheckIn = $today->diffInDays($fromDate, false);
    
        if ($daysUntilCheckIn >= 90) {
            $rate *= 0.8; 
        }
    
       
        $occupancyRate = self::getOccupancyRate($fromDate, $toDate);
    
       
        if ($occupancyRate > 70) {
            $rate *= 1.10; 
        }
    
        $siteLockValue = $request->siteLock === 'on' ? 20 : 0;
    
        $subtotal = $request->subtotal;
        $taxRate = 0.0875;
        $totalTax = $taxRate * $subtotal;
        $total = $subtotal + $totalTax;
        

    
        return [
            'rate' => $rate,
            'subtotal' => $subtotal,
            'taxRate' => $taxRate,
            'totalTax' => $totalTax,
            'total' => $total,
            'numberOfNights' => $numberOfNights,
            'base_rate' => $baseRatePerNight,
        ];
    }

    public static function getOccupancyRate($fromDate, $toDate)
    {
        $totalSites = Site::count();

        $bookedSites = CartReservation::where(function ($query) use ($fromDate, $toDate) {
            $query->whereBetween('cid', [$fromDate, $toDate->subDay()])
                ->orWhereBetween('cod', [$fromDate->addDay(), $toDate])
                ->orWhere(function ($query) use ($fromDate, $toDate) {
                    $query->where('cid', '<=', $fromDate)
                            ->where('cod', '>=', $toDate);
                });
        })->distinct('siteid')->count('siteid');

      
        if ($totalSites == 0) {
            return 0;
        }

        $occupancyRate = ($bookedSites / $totalSites) * 100;

        return $occupancyRate;
    }



    public static function getTierForSiteClass($request)
    {
        $sites = Site::where('siteid', $request->siteId)->first();

   
        if ($request->siteclass === 'RV Sites') {
            // if (in_array($request->hookup, $rvSiteClasses)) {
                

            // } elseif ($request->hookup === 'No Hookup') {
            //     return RateTier::where('tier', 'NOHU')->first();
            // } else {
            //     return null;
            // }
            if($request->hookup !== 'No Hookup'){
                return RateTier::where('tier', $sites->hookup)->first();
            }else {
                return RateTier::where('tier', 'NOHU')->first();
            }
        } elseif ($request->siteclass === 'Boat Slips') {
            if($sites->hookup === Null){
                return RateTier::where('tier', 'BOAT')->first();
            } else {
                return RateTier::where('tier', $sites->hookup)->first();
            }
        } elseif ($request->siteclass === 'Jet Ski Slips') {
           if($sites->hookup === Null){
                return RateTier::where('tier', 'JETSKI')->first();
           } else {
                return RateTier::where('tier', $sites->hookup)->first();
           }
        }elseif($request->siteclass === "Tent Sites"){
            return RateTier::where('tier', $sites->hookup)->first();
        
        } else {
            return RateTier::where('tier', $request->siteclass)->first();
        }
    }
}
