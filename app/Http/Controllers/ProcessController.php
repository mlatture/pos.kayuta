<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\GiftCard;
class ProcessController extends Controller
{

    public function updateGiftCardBalance(Request $request)
    {
        $giftCard = GiftCard::where('barcode', $request->gift_card_number)->first();
        if (!$giftCard) {
            return response()->json(['success' => false, 'message' => 'Gift card not found']);
        } else {
            $giftCard->amount = $request->remaining_balance;
            $giftCard->save();
            return response()->json(['success' => true]);
        }
    }
    public function processGiftCard(Request $request)
    {
        $giftCard = GiftCard::where('barcode', $request->gift_card_number)->first();
        if (!$giftCard) {
            return response()->json(['success' => false, 'message' => 'Gift card not found']);
        } else {
         
            return response()->json(['amount' => $giftCard->amount], 200);
        }
    }
    public function processRefund(Request $request)
    {
        $orderId = $request->order_id;
        $items = $request->items;

        try{
            foreach($items as $item)
            {
                OrderItem::where('order_id', $orderId)
                    ->where('product_id', $item['product_id'])
                    ->delete();
            }

            foreach($items as $item){
                $product = Product::find($item['product_id']);
                if($product)
                {
                    $product->quantity += $item['quantity'];
                    $product->save();
                }
            }

            return response()->json(['success' => true]);
        }catch(Exception $e){
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
