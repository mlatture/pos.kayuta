<?php

namespace App\Http\Controllers;

use App\Models\GiftCard;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Site;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ZoutModel;
use Carbon\Carbon;

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

        $totalSum = $orders->reduce(function($carry, $order){
            if($order->source === 'POS'){
                $price = optional($order->items->first())->price ?? 0; 
            } elseif($order->source === 'Reservation'){
                $price = optional($order->reservations->first())->total ?? 0;
                
            } else {
                $price = 0;
            }
            return $carry + $price;
        }, 0);

        
        if ($request->ajax()) {
            Log::info(["Testing Filters", $orders]);
            return view('reports.components.sales_report_table', compact('orders', 'totalSum'))->render();  
        }
    
        return view('reports.sales-report', compact('orders', 'totalSum'));  
    }

    public function zOutReport(Request $request)
    {
        $customer_id = $request->input('admin_id');
        $dateRange = $request->input('date_range');
        // $stationId = $request->input('station_id');

        $grossSales = ZoutModel::getGrossSales($customer_id, $dateRange);
        $tax = ZoutModel::getTax($customer_id, $dateRange);
        $netSales = ZoutModel::getNetSales($customer_id, $dateRange);
        $salesActivity = ZoutModel::getSalesActivity($customer_id, $dateRange);
        $paymentSummary = ZoutModel::getPaymentSummary($customer_id, $dateRange);
        $creditCardListing = ZoutModel::getCreditCardListing($customer_id, $dateRange);
        $userActivity = ZoutModel::getUserActivity($customer_id, $dateRange);
        if ($request->ajax()) {
            return response()->json([
                'gross_sales' => $grossSales['gross_sales'],
                'transaction_count' => $grossSales['transaction_count'],
                'gross_sales_tax' => $tax,
                'net_sales' => $netSales['gross_sales'],
                'net_tax' => $netSales['total_tax'],
                'net_total_sales' => $netSales['net_sales'],
                'net_transaction_count' => $netSales['transaction_count'],
                'sales_activity' => $salesActivity,
                'payment_summary' => $paymentSummary,
                'list' => $creditCardListing,
                'user_activity' => $userActivity
            ]);
        }

        return view('reports.z-out-report', [
            'gross_sales' => $grossSales,
            'tax' => $tax,
        ]);
    }

    
    
    
    public function incomePerSiteReport(Request $request)
    {   
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'date_to_use' => $request->input('date_to_use', 'reservations.created_at')
        ];
    
        if ($request->has('date_range')) {
            $dates = explode(' - ', $request->date_range);
            $filters['start_date'] = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
            $filters['end_date'] = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
        }
    
        $result = Site::getIncomePersite($filters);
    
        if ($request->ajax()) {
            return response()->json([
                'firstTransactionDate' => $result['firstTransactionDate'],
                'lastTransactionDate' => $result['lastTransactionDate'],
                'totalSum' => number_format($result['totalIncome'], 2),
                'html' => view('reports.components.income-per-site', ['sites' => $result['sites']])->render(),
            ]);
        }
    
        return view('reports.income-per-site-report', [
            'sites' => $result['sites'],
            'totalSum' => $result['totalIncome'],
            'firstTransactionDate' => $result['firstTransactionDate'],
            'lastTransactionDate' => $result['lastTransactionDate'],
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
