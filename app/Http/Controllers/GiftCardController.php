<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGiftCardRequest;
use App\Models\GiftCard;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class GiftCardController extends Controller
{
    private $giftCard;
    protected $user;
    private $object;

    public function __construct(GiftCard $giftCard, User $user)
    {
        $this->middleware('admin_has_permission:'.config('constants.role_modules.list_gift_cards.value'))->only(['index']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.create_gift_cards.value'))->only(['create','store']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.edit_gift_cards.value'))->only(['edit','update']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.delete_gift_cards.value'))->only(['destroy']);
        $this->giftCard =   $giftCard;
        $this->user     =   $user;
        $this->object   =   new BaseController;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $giftCard   =   GiftCard::query();
            if(auth()->user()->organization_id){
                $giftCard->where('organization_id',auth()->user()->organization_id);
            }

            if ($request->barcode) {
                $giftCard->where('barcode', $request->barcode)->first();
            }
            $giftCards = $giftCard->latest()->paginate(10);
            return view('gift-cards.index')->with('giftCards', $giftCards);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('gift-cards.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGiftCardRequest $request)
    {
        try {
            $data   =   $request->except(['_token']);
            $data['organization_id'] = auth()->user()->organization_id;
            $giftCard   =   $this->giftCard->storeGiftCard($data);

            if (!$giftCard) {
                return redirect()->back()->with('error', 'Sorry, Something went wrong while creating gift card.');
            }
            return redirect()->route('gift-cards.index')->with('success', 'Success, New gift card has been added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GiftCard  $giftCard
     * @return \Illuminate\Http\Response
     */
    public function show(GiftCard $giftCard)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GiftCard  $giftCard
     * @return \Illuminate\Http\Response
     */
    public function edit(GiftCard $giftCard)
    {
        if(auth()->user()->organization_id == $giftCard->organization_id || auth()->user()->admin_role_id == 1) {
            return view('gift-cards.edit', compact('giftCard'));
        }
        abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GiftCard  $giftCard
     * @return \Illuminate\Http\Response
     */
    public function update(StoreGiftCardRequest $request, GiftCard $giftCard)
    {
        try {
            $giftCard->title            =   $request->title;
            $giftCard->user_email       =   $request->user_email;
            $giftCard->barcode          =   $request->barcode;
            $giftCard->discount_type    =   $request->discount_type;
            $giftCard->discount         =   $request->discount;
            $giftCard->start_date       =   $request->start_date;
            $giftCard->expire_date      =   $request->expire_date;
            $giftCard->min_purchase     =   $request->min_purchase;
            $giftCard->max_discount     =   $request->max_discount;
            $giftCard->limit            =   $request->limit;
            $giftCard->status           =   $request->status;

            if (!$giftCard->save()) {
                return redirect()->back()->with('error', 'Sorry, Something went wrong while updating the gift card.');
            }
            return redirect()->route('gift-cards.index')->with('success', 'Success, The gift card has been updated.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GiftCard  $giftCard
     * @return \Illuminate\Http\Response
     */
    public function destroy(GiftCard $giftCard)
    {
        $giftCard->delete();

        return response()->json([
            'success' => true
        ]);
    }

    public function appltGiftCard(Request $request)
    {
        try {
            $request->validate([
                'customer_id' => 'required',
                'code'        => 'required'
            ], [
                'customer_id.required'  => 'Customer is required!'
            ]);


            $code   =   $request->code;
            $customer   =   $this->user->findUserById($request->customer_id);

            if (!$customer) {
                return response()->json(['message' => 'Customer Not found!'], 404);
                // return $this->object->respondNotFound(['error' => 'Customer Not found!']);
            }

            $giftCard   =   $this->giftCard->giftCardFind(
                [
                    'barcode' => $code
                ],
                [
                    'date' => date('Y-m-d')
                ]
            );

            if (!$giftCard || ($giftCard->user_email != $customer->email)) {
                return response()->json(['message' => 'Gift Card is not applicable!'], 400);
                // return $this->object->respondBadRequest(['error' => 'Gift Card is not applicable!']);
            }



            $cart = $request->user()->cart()->get();
            $totalAmount    =   0;
            if (count($cart) > 0) {
                foreach ($cart as $cartItems) {
                    $totalAmount    +=   calculatePerProductCartAmount($cartItems);
                }
            }

            $discount = calculateGiftCardDiscount($giftCard, $totalAmount);

            return $this->object->respond(['gift_card_discount' => $discount, 'gift_card' => $giftCard], [], true, 'success!');
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }
}
