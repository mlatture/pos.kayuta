<?php

namespace App\Http\Controllers;

use App\Models\GiftCard;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Site;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    
    private $order;
    private $site;
    private $reservation;
    private $giftCard;

    public function __construct(Order $order, Site $site, Reservation $reservation, GiftCard $giftCard)
    {
        $this->middleware('admin_has_permission:'.config('constants.role_modules.payment_report.value'))->only(['paymentReport']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.reservation_report.value'))->only(['reservationReport']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.sales_report.value'))->only(['salesReport']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.tax_report.value'))->only(['taxReport']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.gift_card_report.value'))->only(['giftCardReport']);
        $this->order        =   $order;
        $this->reservation  =   $reservation;
        $this->giftCard     =   $giftCard;
        $this->site         =   $site;
    }

  public function salesReport(Request $request)
    {
        $filters = $request->only(['date_range', 'date_to_use']); 
        $where = []; 
        
        $orders = $this->order->getAllOrders($where, $filters); 
        
        if ($request->ajax()) {
            Log::info(["Testing Filters", $orders]);
            return view('reports.components.sales_report_table', compact('orders'))->render();  
        }
    
        return view('reports.sales-report', compact('orders'));  
    }
    
    
    

    public function incomePerSiteReport(Request $request)
    {   
        $filters = $request->only(['site_id', 'site_name', 'site_type', 'seasonal']);

        $sites = $this->site->getIncomePersite($filters);
        
        return view('reports.income-per-site-report', [
            'sites' => $sites
        ]);
    }
    
    
    

    public function giftCardReport(Request $request)
    {
        try {
            $where = [];
            if(auth()->user()->organization_id){
                $where['organization_id'] = auth()->user()->organization_id;
            }
            $giftCards =   $this->giftCard->getAllGiftCardWithOrders($where, $request->all());
            return view('reports.gift-card-report')->with('giftCards', $giftCards);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reservationReport(Request $request)
    {
        try {
            $where = [];
            if(auth()->user()->organization_id){
                $where['organization_id'] = auth()->user()->organization_id;
            }
            $reservations =   $this->reservation->getAllReservationsByReport($where, $request->all())->orderBy('cid', 'ASC')->get();
            return view('reports.reservation-report')->with('reservations', $reservations);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function taxReport(Request $request)
    {
        try {
            $where = [];
            if(auth()->user()->organization_id){
                $where['organization_id'] = auth()->user()->organization_id;
            }
            $orders =   $this->order->getAllOrders($where, $request->all());
            return view('reports.tax-report')->with('orders', $orders);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function paymentReport(Request $request)
    {
        try {
            $where = [];
            if(auth()->user()->organization_id){
                $where['organization_id'] = auth()->user()->organization_id;
            }
            $orders =   $this->order->getAllOrders($where, $request->all());
            return view('reports.payment-report')->with('orders', $orders);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
