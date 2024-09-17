<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\GiftCard;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            // $this->saveTransaction($response);
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
        $paymentMethod = $request->payment_method;
        $paymentAccNumber = $request->payment_acc_number;
        $totalAmount = $request->total_amount;


        DB::beginTransaction();

        try {
            foreach ($items as $item) {
                Log::info('Deleting order item', [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                ]);
                OrderItem::where('order_id', $orderId)
                    ->where('product_id', $item['product_id'])
                    ->delete();
            }

            if ($paymentMethod === 'GiftCard') {
                $totalRefundAmount = 0;



                foreach ($items as $item) {
                    $product = Product::find($item['product_id']);
                    $giftCard = GiftCard::where('barcode', $paymentAccNumber)->first();

                    if ($product && $giftCard) {
                        $totalRefundAmount += $item['price'];

                        $product->quantity += $item['quantity'];
                        $product->save();
                    } else {
                        Log::warning('Product or gift card not found', [
                            'product_id' => $item['product_id'],
                            'gift_card_number' => $paymentAccNumber,
                        ]);
                    }
                }

                if ($giftCard) {
                    $giftCard->amount += $totalRefundAmount;
                    $giftCard->save();
                } else {
                    Log::warning('Gift card not found for updating amount', [
                        'gift_card_number' => $paymentAccNumber,
                    ]);
                }
            } elseif ($paymentMethod === 'CreditCard') {
                $originalTransactionRefNum = $this->getOriginalTransactionRefNum($orderId);

                $refundResponse = $this->processCreditCardRefund($totalAmount, $paymentAccNumber, $originalTransactionRefNum);

                if ($refundResponse['success']) {
                    Log::info('Credit card refund successful', [
                        'transaction_data' => $refundResponse['transaction_data']
                    ]);
                } else {
                    Log::error('Credit card refund failed', [
                        'error' => $refundResponse['error']
                    ]);
                    return response()->json(['success' => false, 'error' => $refundResponse['error']]);
                }
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Refund processing failed', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }


    private function processCreditCardRefund($totalAmount, $paymentAccNumber, $refNum)
    {
        $cardknoxUrl = 'https://x1.cardknox.com/gateway';
        $cardknoxApiKey = config('services.cardknox.api_key');

        $payload = [
            'xKey' => $cardknoxApiKey,
            'xCommand' => 'cc:refund',
            'xVersion' => '4.5.5',
            'xSoftwareName' => 'Kayutalake',
            'xSoftwareVersion' => '1.0',
            'xAmount' => $totalAmount,
            'xCardNum' => $paymentAccNumber,
            'xExp' => '0831',
            'xAllowDuplicate' => true,
            'xRefNum' => $refNum,
        ];

        $response = $this->makeCurlRequest($cardknoxUrl, $payload);
        if (isset($response['xResult']) && $response['xResult'] == 'A') {
            return [
                'success' => true,
                'transaction_data' => $response
            ];
        } else {
            $errorMsg = $response['xError'] ?? 'Unknown error';
            return [
                'success' => false,
                'error' => $errorMsg,
            ];
        }
    }

    private function getOriginalTransactionRefNum($orderId)
    {
      
        return DB::table('orders')->where('id', $orderId)->value('admin_id');
    }


}
