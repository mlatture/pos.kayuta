<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Reservation;
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
        
        $reservations = $reservationsQuery->where('created_at', '>=', date('Y-m-1'))->get();
        $customers_count = $customersQuery->count();
        $site_count = $sitesQuery->count();
        $orders = $ordersQuery->where('created_at', '>=', date('Y-m-1'))->get();
    
        $today_reservations = $reservations->where('created_at', '>=', date('Y-m-d'));
        $today_orders = $orders->where('created_at', '>=', date('Y-m-d'));
    
        // Calculate total income
        $reservations_income = $reservations->sum('total');
        $orders_income = $orders->sum('amount');
        $total_income = $reservations_income + $orders_income;
    
        // Calculate today's income
        $today_reservations_income = $today_reservations->sum('total');
        $today_orders_income = $today_orders->sum('amount');
        $income_today = $today_reservations_income + $today_orders_income;
    
        $currentMonth = now()->month;
    
        $best_products = $bestProductsQuery->whereHas('orderItems', function ($query) use ($currentMonth) {
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
            'reservation_count' => $reservations->count(),
            'today_reservations' => $today_reservations->count(),
            'income' => $total_income,
            'income_today' => $income_today,
            'customers_count' => $customers_count,
            'site_count' => $site_count,
            'best_products' => $best_products,
        ]);
    }
    
}
