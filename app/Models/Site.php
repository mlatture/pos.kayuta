<?php

namespace App\Models;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
class Site extends Model
{
    use HasFactory;

    protected $table = 'sites';

    protected $guarded = [];
    private $cit = ' 15:00:00';
    private $cot = ' 10:00:00';

    
    protected $casts = [
        'images' => 'array',
        'rigtypes' => 'array',
        'amenities' => 'array',
    ];

    protected $fillable = ['sitename', 'siteclass', 'seasonal', 'siteid', 'available', 'availableonline', 'images'];

    public static function getIncomePersite($filters = [])
    {
        $query = self::query()
            ->leftJoin('reservations', 'sites.siteid', '=', 'reservations.siteid')
            ->selectRaw(
                '
            sites.siteid as site_id,
            sites.sitename as site_name,
            sites.siteclass as site_type,
            sites.seasonal,
            COUNT(reservations.id) as nights_occupied,
            SUM(reservations.total) as income_from_stays,
            MIN(reservations.created_at) as first_transaction_date,
            MAX(reservations.created_at) as last_transaction_date
        ',
            )
            ->groupBy('sites.siteid', 'sites.sitename', 'sites.siteclass', 'sites.seasonal');

        $dateColumn = 'reservations.created_at';

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $dateMapping = [
                'arrival_date' => 'reservations.checkedin',
                'transaction_date' => 'reservations.created_at',
                'checkin_date' => 'reservations.cod',
            ];

            if ($filters['date_to_use'] === 'stay_on') {
                $query->where(function ($q) use ($filters) {
                    $q->whereBetween('reservations.checkedin', [$filters['start_date'], $filters['end_date']])
                        ->orWhereBetween('reservations.checkedout', [$filters['start_date'], $filters['end_date']])
                        ->orWhere(function ($subQuery) use ($filters) {
                            $subQuery->where('reservations.checkedin', '<=', $filters['start_date'])->where('reservations.checkedout', '>=', $filters['end_date']);
                        });
                });
            } else {
                $dateColumn = $dateMapping[$filters['date_to_use']] ?? 'reservations.created_at';
                $query->whereBetween($dateColumn, [$filters['start_date'], $filters['end_date']]);
            }

            Log::info('Using Date Column:', [
                'selected_date_to_use' => $filters['date_to_use'],
                'resolved_column' => $dateColumn ?? 'stay_on (checkedin to checkedout)',
                'start_date' => $filters['start_date'],
                'end_date' => $filters['end_date'],
            ]);
        }

        if (!empty($filters['site_id'])) {
            $query->where('sites.siteid', $filters['site_id']);
        }

        if (!empty($filters['site_name'])) {
            $query->where('sites.sitename', 'like', '%' . $filters['site_name'] . '%');
        }

        if (!empty($filters['site_type'])) {
            $query->where('sites.siteclass', $filters['site_type']);
        }

        if (isset($filters['seasonal'])) {
            $query->where('sites.seasonal', $filters['seasonal']);
        }

        $sites = $query->get()->map(function ($site) {
            $totalDays = Carbon::now()->dayOfYear;
            $site->percent_occupancy = $totalDays > 0 ? round(($site->nights_occupied / $totalDays) * 100, 2) : 0;
            return $site;
        });

        $firstTransactionDate = $sites->whereNotNull('first_transaction_date')->pluck('first_transaction_date')->min();
        $lastTransactionDate = $sites->whereNotNull('last_transaction_date')->pluck('last_transaction_date')->max();

        Log::info('Filtered Data:', [
            'start_date' => $filters['start_date'],
            'end_date' => $filters['end_date'],
            'date_to_use' => $filters['date_to_use'] ?? 'reservations.created_at',
            'using_column' => $dateColumn,
            'firstTransactionDate' => $firstTransactionDate,
            'lastTransactionDate' => $lastTransactionDate,
        ]);

        return [
            'sites' => $sites,
            'totalIncome' => $sites->sum('income_from_stays'),
            'firstTransactionDate' => $firstTransactionDate ? Carbon::parse($firstTransactionDate)->format('F j, Y') : 'N/A',
            'lastTransactionDate' => $lastTransactionDate ? Carbon::parse($lastTransactionDate)->format('F j, Y') : 'N/A',
        ];
    }

    public function getTotalDaysAttribute()
    {
        return $this->attributes['total_days'];
    }

    public function setTotalDaysAttribute($value)
    {
        $this->attributes['total_days'] = $value;
    }

    public function getAllPaginate()
    {
        return self::orderBy('id', 'DESC')->paginate(10);
    }

    public function getAll()
    {
        return self::orderBy('id', 'DESC')->get();
    }

    public function getAllActive()
    {
        return self::where('status', 1)->get();
    }

    public function createSite($data = [])
    {
        return self::create($data);
    }

    public function findSite($id)
    {
        return self::find($id);
    }

    public function whereGet($where = [])
    {
        return self::where($where)->get();
    }

    public function whereUpdate($where = [], $data = [])
    {
        return self::where($where)->update($data);
    }

    public function getAllSiteWithReservations($where = [], $filters = [], $siteIds = [])
    {
        $queryBuilder = self::with('reservations')->where($where);
        if (count($siteIds) > 0) {
            $queryBuilder = $queryBuilder->whereIn('id', $siteIds);
        }
        $queryBuilder = $queryBuilder->when(count($filters) > 0, function ($query) use ($filters) {
            $query->when(!empty($filters['startDate']), function ($q) use ($filters) {
                $q->whereHas('reservations', function ($q) use ($filters) {
                    $q->where('cid', '>', $filters['startDate'])->where('cod', '<', $filters['endDate']);
                });
            });
        });
        return $queryBuilder;
    }

    public function checkAllSites($cid, $cod, $siteId = null)
    {
        return $this->select(['sites.siteid', DB::raw('1 AS status'), 'sitename', 'hookup', 'availableonline', 'available', 'seasonal', 'maxlength', 'minlength', 'rigtypes', 'class', 'coordinates', DB::raw("COALESCE(cart_reservations.siteid, 'Available') AS incart"), DB::raw("COALESCE(reservations.siteid, 'Available') AS reserved"), DB::raw("COALESCE(cart_reservations.cartid, '') AS cartid")])
            ->leftJoin('reservations', function ($join) use ($cid, $cod) {
                $join->on('sites.siteid', '=', 'reservations.siteid')->where(function ($query) use ($cid, $cod) {
                    $query
                        ->whereBetween('reservations.cid', [$cid . ' ' . $this->cit, $cod . ' ' . $this->cot])
                        ->orWhereBetween('reservations.cod', [$cid . ' ' . $this->cit, $cod . ' ' . $this->cot])
                        ->orWhere(function ($query) use ($cid, $cod) {
                            $query->whereBetween(DB::raw("'" . $cid . ' ' . $this->cit . "'"), [DB::raw('reservations.cid'), DB::raw('reservations.cod')])->orWhereBetween(DB::raw("'" . $cod . ' ' . $this->cot . "'"), [DB::raw('reservations.cid'), DB::raw('reservations.cod')]);
                        });
                });
            })
            ->leftJoin('cart_reservations', function ($join) use ($cid, $cod) {
                $join->on('sites.siteid', '=', 'cart_reservations.siteid')->where(function ($query) use ($cid, $cod) {
                    $query
                        ->whereBetween('cart_reservations.cid', [$cid . ' ' . $this->cit, $cod . ' ' . $this->cot])
                        ->orWhereBetween('cart_reservations.cod', [$cid . ' ' . $this->cit, $cod . ' ' . $this->cot])
                        ->orWhere(function ($query) use ($cid, $cod) {
                            $query->whereBetween(DB::raw("'" . $cid . ' ' . $this->cit . "'"), [DB::raw('cart_reservations.cid'), DB::raw('cart_reservations.cod')])->orWhereBetween(DB::raw("'" . $cod . ' ' . $this->cot . "'"), [DB::raw('cart_reservations.cid'), DB::raw('cart_reservations.cod')]);
                        });
                });
                // ->where(\DB::raw('cart_reservations.holduntil'), '>', \DB::raw('NOW()'));
            })
            ->where([['available', '=', 1], ['availableonline', '=', 1], ['seasonal', '=', 0]])
            ->when(!empty($siteId), function ($q) use ($siteId) {
                $q->where('sites.siteid', $siteId);
            });
    }

    // public function flexibleSites($cid, $cod, $riglen, $siteclass)
    // {
    //     $query = self::select([
    //         'sites.siteid',
    //         'sites.maxlength',
    //         'sites.minlength',
    //         'sites.hookup',
    //         'sites.class',
    //     ])
    //         ->selectRaw("GROUP_CONCAT(IFNULL(IF(reservations.siteid IS NOT NULL OR cart_reservations.siteid IS NOT NULL, 'N', 'A') ORDER BY dates.date) AS availability")
    //         ->from('sites')
    //         ->crossJoin(
    //             DB::raw("(SELECT DATE_ADD(?, INTERVAL n DAY) AS date FROM (SELECT a.N + b.N * 10 + 1 AS N FROM (SELECT 0 AS N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) a, (SELECT 0 AS N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) b ORDER BY N) numbers WHERE DATE_ADD(?, INTERVAL n DAY) <= ?) AS dates"),
    //             [$cid, $cid, $cod] // Use placeholders for date values
    //         )
    //         ->leftJoin('reservations', function ($join) {
    //             $join->on('sites.siteid', '=', 'reservations.siteid')
    //                 ->whereRaw('reservations.cid < DATE_ADD(dates.date, INTERVAL 1 DAY)')
    //                 ->whereRaw('reservations.cod > dates.date');
    //         })
    //         ->leftJoin('cart_reservations', function ($join) {
    //             $join->on('sites.siteid', '=', 'cart_reservations.siteid')
    //                 ->whereRaw('cart_reservations.cid < DATE_ADD(dates.date, INTERVAL 1 DAY)')
    //                 ->whereRaw('cart_reservations.cod > dates.date');
    //         });

    //     if ($riglen > 1) {
    //         $query->whereRaw('sites.minlength <= ? AND sites.maxlength >= ?', [$riglen, $riglen]);
    //     }

    //     $query->where('sites.class', 'LIKE', '%' . $siteclass . '%')
    //         ->groupBy('sites.siteid');

    //     // You can uncomment the following line for debugging if needed.
    //     // \DB::enableQueryLog();

    //     // Execute the query and return the results
    //     $events = $query->get();

    //     // You can log the generated SQL query for debugging as follows:
    //     // $sql = \DB::getQueryLog();
    //     // Log::Site(end($sql)['query']);

    //     return $events;
    // }

    public function flexibleSites($cid, $cod, $riglen, $siteclass)
    {
        $lengthsql = '';
        if ($riglen > 1) {
            $lengthsql = "minlength <= $riglen AND maxlength >= $riglen AND ";
        }

        $events = Site::select(['sites.siteid', 'sites.maxlength', 'sites.minlength', 'sites.hookup', 'sites.class'])
            ->selectRaw("GROUP_CONCAT(IFNULL(IF(r.siteid IS NOT NULL OR c.siteid IS NOT NULL, 'N', 'A'), 'Available') ORDER BY d.date) AS availability")
            ->crossJoin(
                DB::raw(
                    "(SELECT
        DATE_ADD('" .
                        $cid .
                        "', INTERVAL n DAY) AS date
        FROM
        (SELECT a.N + b.N * 10 + 1 AS N FROM (SELECT 0 AS N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) a, (SELECT 0 AS N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) b ORDER BY N) numbers
        WHERE
        DATE_ADD('" .
                        $cid .
                        "', INTERVAL n DAY) <= '" .
                        $cod .
                        "') d",
                ),
            )
            ->leftJoin('reservations as r', function ($join) {
                $join->on('sites.siteid', '=', 'r.siteid')->whereRaw('r.cid < DATE_ADD(d.date, INTERVAL 1 DAY)')->whereRaw('r.cod > d.date');
            })
            ->leftJoin('cart_reservations as c', function ($join) {
                $join->on('sites.siteid', '=', 'c.siteid')->whereRaw('c.cid <= DATE_ADD(d.date, INTERVAL 1 DAY)')->whereRaw('c.cod >= d.date');
            })
            ->whereRaw("$lengthsql class LIKE '%$siteclass%'")
            ->groupBy('sites.siteid', 'sites.maxlength', 'sites.minlength', 'sites.hookup', 'sites.class')
            ->get();

        return $events;
    }

    public function checkAvailable($cid, $cod, $tier)
    {
        $cit = $this->cit;
        $cot = $this->cot;

        $availability = $this->where('ratetier', $tier)
            ->whereNotIn('siteid', function ($query) use ($cid, $cod, $cit, $cot) {
                $query
                    ->select('siteid')
                    ->from('reservations')
                    ->where(function ($query) use ($cid, $cod, $cit, $cot) {
                        $query
                            ->whereRaw("CONCAT('$cid', '$cit') between cid and cod")
                            ->orWhereRaw("CONCAT('$cod', '$cot') between cid and cod")
                            ->orWhere(function ($query) use ($cid, $cod, $cit, $cot) {
                                $query->where('cid', '>=', DB::raw("CONCAT('$cid', '$cit')"))->where('cod', '<=', DB::raw("CONCAT('$cod', '$cot')"));
                            });
                    });
            })
            ->first();

        if ($availability) {
            Session::put('numrows', 1);
        } else {
            Session::put('numrows', 0);
        }

        return $availability;
    }

    public function checkBooked($cid, $cod, $tier)
    {
        $cit = $this->cit;
        $cot = $this->cot;

        $availability = $this->where('ratetier', $tier)
            ->whereIn('siteid', function ($query) use ($cid, $cod, $cit, $cot) {
                $query
                    ->select('siteid')
                    ->from('reservations')
                    ->where(function ($query) use ($cid, $cod, $cit, $cot) {
                        $query
                            ->whereRaw("CONCAT('$cid', '$cit') between cid and cod")
                            ->orWhereRaw("CONCAT('$cod', '$cot') between cid and cod")
                            ->orWhere(function ($query) use ($cid, $cod, $cit, $cot) {
                                $query->where('cid', '>=', DB::raw("CONCAT('$cid', '$cit')"))->where('cod', '<=', DB::raw("CONCAT('$cod', '$cot')"));
                            });
                    });
            })
            ->first();

        if ($availability) {
            Session::get('numrows', 1);
        } else {
            Session::get('numrows', 0);
        }

        return $availability;
    }

    public function whereFirst($where = [])
    {
        return self::where($where)->first();
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'siteid', 'siteid');
    }
}
