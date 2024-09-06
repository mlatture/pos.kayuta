<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin_has_permission:'.config('constants.role_modules.dashboard.value'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $reservationsQuery = Reservation::query();
        $customersQuery = User::query();
        $sitesQuery = Site::query();
        $ordersQuery = Order::query();
        $bestProductsQuery = Product::query();
     
        if(auth()->user()->organization_id){
            $reservationsQuery->where('organization_id',auth()->user()->organization_id);
            $customersQuery->where('organization_id',auth()->user()->organization_id);
            $sitesQuery->where('organization_id',auth()->user()->organization_id);
            $ordersQuery->where('organization_id',auth()->user()->organization_id);
            $bestProductsQuery->where('organization_id',auth()->user()->organization_id);
        }

        $reservations       =   $reservationsQuery->where('created_at', '>=', date('Y-m-1'))->get();
        $customers_count    =   $customersQuery->count();
        $site_count         =   $sitesQuery->count();
        $order              =   $ordersQuery->where('created_at', '>=', date('Y-m-1'))->get();

        $today_reservations =   $reservations->where('created_at', '>=', date('Y-m-d'));
        $today_orders       =   $order->where('created_at', '>=', date('Y-m-d'));
        $total_income       =   $reservations->sum('total') + $order->sum('amount');
        $income_today       =   $reservations->sum('total') + $order->sum('amount');

        $currentMonth       =   now()->month;

        $best_products      =   $bestProductsQuery->whereHas('orderItems', function ($query) use ($currentMonth) {
            $query->whereHas('order', function ($query) use ($currentMonth) {
                $query->whereMonth('created_at', $currentMonth);
            });
        })
        ->withCount(['orderItems as total_quantity' => function ($query) use ($currentMonth) {
            $query->whereHas('order', function ($query) use ($currentMonth) {
                $query->whereMonth('created_at', $currentMonth);
            });
        }])
        ->orderByDesc('total_quantity')
        ->first();

        return view('home', [
            'reservation_count'     =>  $reservations->count(),
            'today_reservations'    =>  $today_reservations->count(),
            'income'                =>  $total_income,
            'income_today'          =>  $income_today,
            'customers_count'       =>  $customers_count,
            'site_count'            =>  $site_count,
            'best_products'         =>  $best_products,
        
        ]);
    }
}
