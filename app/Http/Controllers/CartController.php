<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StationRegisters;
use App\Models\UpsellRate;
use App\Models\UpsellText;
use App\Models\UpsellOrder;
class CartController extends Controller
{
    private $object;

    public function __construct()
    {
        $this->object = new BaseController();
        $this->middleware(function ($request, $next) {
            if (auth()->user()->hasPermission('pos_management')) {
                return $next($request);
            }
            abort(403);
        });
    }

    public function index(Request $request)
    {
        $registers = StationRegisters::all();
       
        if ($request->wantsJson()) {
            return response(
                $request->user()->cart()->get()
            );
        }
        $cart = $request->user()->cart()->get();
        $customersQuery = User::query();
        $productsQuery = Product::where('status', '=', 1)->where('quantity', '!=', 0);
        $categoriesQuery = Category::query();
      
        $customers = $customersQuery->get();
        $products = $productsQuery->get();
        $categories = $categoriesQuery->get();
        return view('cart.index', compact('customers', 'cart', 'products', 'categories', 'registers'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'barcode' => 'nullable|exists:products,barcode',
                'product_id' => 'nullable|exists:products,id',
            ]);

            $barcode = $request->barcode;
            $productId = $request->product_id;

            if (!$barcode && !$productId) {
                return $this->object->respondBadRequest(['error' => "Bar code or Product ID is Required!"]);
            }

            if ($barcode) {
                $product = Product::with(['taxType'])->where('barcode', $barcode)->first();
                $cart = $request->user()->cart()->where('barcode', $barcode)->first();
            } else if ($productId) {
                $product = Product::with(['taxType'])->where('id', $productId)->first();
                $cart = $request->user()->cart()->where('products.id', $productId)->first();
            }

            if ($cart) {
                // Check if the product has limited stock (non-negative) and if the cart quantity exceeds available stock
                if ($product->quantity >= 0 && $product->quantity <= $cart->pivot->quantity) {
                    return $this->object->respondBadRequest(['error' => 'Product available only: ' . $product->quantity]);
                }

                // Handle discount
                if ($product->discount_type == 'fixed_amount') {
                    $cart->pivot->discount += $product->discount;
                } else if ($product->discount_type == 'percentage') {
                    $cart->pivot->discount += ($product->price * $product->discount) / 100;
                }

                // Handle tax
                if ($product->taxType) {
                    if ($product->taxType->tax_type == 'fixed_amount') {
                        $cart->pivot->tax += $product->taxType->tax;
                    } else if ($product->taxType->tax_type == 'percentage') {
                        $cart->pivot->tax += ($product->price * $product->taxType->tax) / 100;
                    }
                }

                // Update quantity in cart
                $cart->pivot->quantity += 1;
                $cart->pivot->save();
            } else {
                // Check if product is out of stock (ignore negative quantities for unlimited stock)
                if ($product->quantity >= 0 && $product->quantity < 1) {
                    return $this->object->respondBadRequest(['error' => 'Product out of stock']);
                }

                // Handle discount
                $discount = 0;
                if ($product->discount_type == 'fixed_amount') {
                    $discount = $product->discount;
                } else if ($product->discount_type == 'percentage') {
                    $discount = ($product->price * $product->discount) / 100;
                }

                // Handle tax
                $tax = 0;
                if ($product->taxType) {
                    if ($product->taxType->tax_type == 'fixed_amount') {
                        $tax = $product->taxType->tax;
                    } else if ($product->taxType->tax_type == 'percentage') {
                        $tax = ($product->price * $product->taxType->tax) / 100;
                    }
                }

                // Add to cart (no stock limit if quantity is negative)
                $request->user()->cart()->attach($product->id, [
                    'quantity' => 1,
                    'discount' => $discount,
                    'tax' => $tax
                ]);
            }

            $upsellMessage = $this->handleUpsell($product->id, auth()->user()->name);

            DB::commit();

            return $this->object->respond([
                'upsell_message' => $upsellMessage,
                'cart' => $request->user()->cart()->get()], 
                [], true, 'Product added!',
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->object->respondBadRequest(['error' => $e->getMessage()]);
        }
    }

    private function handleUpsell($orderNumber, $cashier)
    {
        $upsellRate = UpsellRate::orderBy('created_at', 'desc')->first();
        $ratePercent = $upsellRate ? $upsellRate->rate_percent : 50.00;
        
        $showUpsell = rand(0, 100) < $ratePercent;

        if($showUpsell){
            $upsellText = UpsellText::where('active_message', true)->inRandomOrder()->first();

            if($upsellText){
                UpsellOrder::create([
                    'order_number' => $orderNumber,
                    'cashier' => $cashier,
                    'upsell_text_id' => $upsellText->id,
                ]);


                return $upsellText->message_text;
            } else {
               return null;
            }
        } else {
            return null;
        }

        return null;
    }


    public function changeQty(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);
            DB::beginTransaction();

            $cart = $request->user()->cart()->where('products.id', $request->product_id)->first();

            if ($cart) {
                $product = Product::where('id', $request->product_id)->first();
                if ($product->quantity < $request->quantity) {
                    return $this->object->respondBadRequest(['error' => 'Product available only: ' . $product->quantity]);
                }

                $discount = 0;
                if ($product->discount_type == 'fixed_amount') {
                    $discount = $product->discount;
                } else if ($product->discount_type == 'percentage') {
                    $discount = ($product->price * $product->discount) / 100;
                }

                $tax = 0;
                if ($product->taxType) {
                    if ($product->taxType->tax_type == 'fixed_amount') {
                        $tax = $product->taxType->tax;
                    } else if ($product->taxType->tax_type == 'percentage') {
                        $tax = ($product->price * $product->taxType->tax) / 100;
                    }
                }

                $cart->pivot->discount = $discount * $request->quantity;
                $cart->pivot->tax = $tax * $request->quantity;

                $cart->pivot->quantity = $request->quantity;
                $cart->pivot->save();
            }

            DB::commit();

            return $this->object->respond($request->user()->cart()->get(), [], true, 'Quantity updated!');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->object->respondBadRequest(['error' => $e->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'product_id' => 'required|integer|exists:products,id'
            ]);

            $request->user()->cart()->detach($request->product_id);

            DB::commit();
            return $this->object->respond($request->user()->cart()->get(), [], true, 'Product Deleted!');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->object->respondBadRequest(['error' => $e->getMessage()]);
        }
    }

    public function empty(Request $request)
    {
        if (count($request->user()->cart()->get()) > 0) {
            $request->user()->cart()->detach();
            return $this->object->respond($request->user()->cart()->get(), [], true, 'Cart Deleted!');
        }
        return $this->object->respondBadRequest(['error' => 'Cart is already empty']);
    }


    public function showPartialPaymentCustomer()
    {
       
        $customers = Customer::all();
    
        $payments = [];
    
      
        foreach ($customers as $customer) {
        
            $orders = Order::where('user_id', $customer->id)->get();
    
        
            foreach ($orders as $order) {
                $orderPayments = PosPayment::where('order_id', $order->id)->get();
    
               
                foreach ($orderPayments as $payment) {
                    $payments[] = $payment;
                }
            }
        }
    
     
        return response()->json([
            'success' => $payments,
        ]);
    }
    
}
