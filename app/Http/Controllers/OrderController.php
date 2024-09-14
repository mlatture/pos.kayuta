<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Jobs\SendOrderReceiptJob;
use App\Models\Order;
use App\Models\Reservation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem;
use App\Models\Product;
class OrderController extends Controller
{
    private $object;

    public function __construct()
    {
        $this->middleware('admin_has_permission:'.config('constants.role_modules.orders.value'));
        $this->object   =   new BaseController;
    }

    public function index(Request $request)
    {
        $orders = Order::query();
        // if(auth()->user()->organization_id){
        //     $orders->where('organization_id',auth()->user()->organization_id);
        // }
        if ($request->start_date) {
            $orders = $orders->where('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $orders = $orders->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }
        $orders = $orders->with(['items', 'payments', 'customer'])->latest()->paginate(10);

        $total = $orders->map(function ($i) {
            return $i->total();
        })->sum();
        $receivedAmount = $orders->map(function ($i) {
            return $i->receivedAmount();
        })->sum();

        return view('orders.index', compact('orders', 'total', 'receivedAmount'));
    }

    public function ordersToBeReturn(Request $request)
    {
         $order = Order::findOrFail($request->order_id);
         $orderItems = $order->orderItems()->with('product')->get();
        
         return response()->json($orderItems);
    }

   

    public function store(OrderStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $order = Order::create([
                'organization_id' => auth()->user()->organization_id,
                'user_id' => $request->customer_id ?? 0,
                'gift_card_id' => $request->gift_card_id ?? 0,
                'admin_id' => $request->user()->id,
                'amount' => $request->amount,
                'gift_card_amount'  =>  $request->gift_card_discount ?? 0
            ]);

            $cart = $request->user()->cart()->get();
            foreach ($cart as $item) {
                // $price = $item->price + $item->pivot->tax - $item->pivot->discount;
                $order->items()->create([
                    'price' => ($item->price * $item->pivot->quantity) + $item->pivot->tax - $item->pivot->discount,
                    'quantity' => $item->pivot->quantity,
                    'tax'       =>  $item->pivot->tax,
                    'discount'       =>  $item->pivot->discount,
                    'product_id' => $item->id,
                ]);
                $item->quantity = $item->quantity - $item->pivot->quantity;
                $item->save();
            }
            $request->user()->cart()->detach();
            $order->payments()->create([
                'amount' => $request->amount,
                'admin_id' => $request->user()->id,
            ]);

            $order = Order::orderFindById($order->id);

            // dispatch(new SendOrderReceiptJob($order));

            DB::commit();

            return response()->json(['success', 'Order Placed Successfully!']);
            // return back()->with('success', 'Order Placed Successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->object->respondBadRequest(['error' => $e->getMessage()]);
        }
    }

    public function generateInvoice($id)
    {
        try {
            $order = Order::orderFindById($id);
            if(auth()->user()->organization_id == $order->organization_id || auth()->user()->admin_role_id == 1){
                if (empty($order)) {
                    return redirect()
                        ->back()
                        ->with('error', 'Order not found.')
                        ->withInput();
                }

                return view('orders.invoice', compact('order'));
            }
            abort(403,'Forbidden');
        } catch (\Exception $exception) {
            if($exception->getMessage() == 'Forbidden') {
                abort(403);
            }
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
    }

    
}
