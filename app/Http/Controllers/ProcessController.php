<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\GiftCard;
use Illuminate\Support\Facades\Http;

class ProcessController extends Controller
{

    public function makeCurlRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        $responseContent = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['error' => 'Curl error: ' . $error];
        }

        curl_close($ch);

    
        parse_str($responseContent, $responseArray);

        if ($responseArray) {
            return $responseArray;
        } else {
            return ['error' => 'No response from the server.'];
        }
    }


    public function processCreditCard(Request $request)
    {
        $cardknoxUrl = 'https://x1.cardknox.com/gateway';
        $cardknoxApiKey = config('services.cardknox.api_key');

        $payload = [
            'xKey' => $cardknoxApiKey,
            'xCommand' => 'cc:sale',
            'xVersion' => '4.5.5',
            'xSoftwareName' => 'Kayutalake',
            'xSoftwareVersion' => '1.0',
            'xAmount' => $request->amount,
            'xCardNum' => $request->ccnum,
            'xExp' => $request->exp,
            'xAllowDuplicate' => 'TRUE',
        ];

        $response = $this->makeCurlRequest($cardknoxUrl, $payload);

        if (isset($response['xResult']) && $response['xResult'] == 'A') {
            return response()->json([
                'message' => 'Payment Approved',
                'transaction_data' => $response,
            ]);
        } else {
            $errorMessage = $response['xError'] ?? 'Unknown error';
            return response()->json([
                'message' => 'Payment Declined with error: ' . $errorMessage,
                'error' => $errorMessage,
              
            ], 400);
        }
    }



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

        try {
            foreach ($items as $item) {
                OrderItem::where('order_id', $orderId)
                    ->where('product_id', $item['product_id'])
                    ->delete();
            }

            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->quantity += $item['quantity'];
                    $product->save();
                }
            }

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
