<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RateTier;
use Carbon\Carbon;
use App\Models\Site;
use Illuminate\Support\Facades\Log;
use App\Models\CartReservation;
use Illuminate\Support\Collection;

class SitesAvailable extends Model
{
    use HasFactory;

    public static function getAvailableSites($dateNow)
    {
        $date = Carbon::parse($dateNow)->startOfDay();  
    
        $availableSites = Site::whereNotIn('siteid', function ($query) use ($date) {
            $query->select('siteid')
                  ->from('reservations')
                  ->where('cod', '>=', $date)
                  ->orWhere('cid', '<=', $date);  
        })
        ->select('sites.id', 'sites.siteid', 'sites.ratetier', 'sites.siteclass')
        ->distinct()
        ->get();
    
        return $availableSites;
    }
    
    
    
    
  
}

