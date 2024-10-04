<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\GiftCard;
use Illuminate\Support\Facades\Log;

class PayBalanceController extends Controller
{
    public function payBalance(Request $request, $cartid)
    {
        $paymentType = $request->paymentType;
        $paymentAmount = $request->xAmount ?? 0;

        $payment = Payment::where('cartid', $cartid)->firstOrFail();

        try {
            switch ($paymentType) {
                case 'Cash':
                case 'Other':
                    $payment->payment += $paymentAmount;
                    $payment->save();
                    break;

                case 'Manual':
                case 'Check':
                    $responseArray = $this->processCardknoxPayment($request, $paymentType);
                    if ($responseArray['xStatus'] === 'Approved') {
                        $payment->payment += $paymentAmount;
                        $payment->save();
                    } else {
                        Log::error('Payment failed', ['response' => $responseArray]);
                        return response()->json([
                            'message' => 'Payment failed: ' . ($responseArray['xError'] ?? 'Unexpected error occurred.')
                        ], 400);
                    }
                    break;

                case 'Gift Card':
                    $giftcard = GiftCard::where('barcode', $request->xBarcode)->firstOrFail();
                    if ($giftcard->amount < $paymentAmount) {
                        return response()->json(['message' => 'Insufficient gift card balance.'], 400);
                    }
                    $giftcard->amount -= $paymentAmount;
                    $giftcard->save();

                    $payment->payment += $paymentAmount;
                    $payment->save();
                    break;

                default:
                    return response()->json(['message' => 'Invalid payment type.'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Payment processing error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'An error occurred during payment processing.'], 500);
        }

        return response()->json(['success' => true]);
    }

    private function processCardknoxPayment(Request $request, $paymentType)
    {
        $apiKey = config('services.cardknox.api_key');
        $data = [
            'xKey'             => $apiKey,
            'xVersion'         => '4.5.5',
            'xCommand'         => $paymentType === 'Manual' ? 'cc:sale' : 'check:sale',
            'xAmount'          => $request->xAmount,
            'xSoftwareVersion' => '1.0',
            'xSoftwareName'    => 'KayutaLake',
            'xRouting'         => $paymentType === 'Check' ?  $request->xRouting : '',
            'xAccount'         => $paymentType === 'Check' ? $request->xAccount : '',
            'xName'            => $paymentType === 'Check' ? $request->xName : '',
        ];

        if ($paymentType === 'Manual') {
            $data['xCardNum'] = $request->input('xCardNum');
            $data['xExp']     = str_replace('/', '', $request->xExp);
        } else {
            $data['xAccount'] = $request->input('xAccount');
        }

        $ch = curl_init('https://x1.cardknox.com/gateway');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_HTTPHEADER     => [
                'Content-type: application/x-www-form-urlencoded',
                'X-Recurring-Api-Version: 1.0',
            ],
            CURLOPT_SSL_VERIFYPEER => false, 
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $responseContent = curl_exec($ch);
        if ($responseContent === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception('Error communicating with payment gateway: ' . $error);
        }
        curl_close($ch);

        parse_str($responseContent, $responseArray);

        return $responseArray;
    }

    public function processCreditCardTerminal(Request $request, $id)
    {
        $paymentAmount = $request->xAmount ?? 0;
        $invoiceRandom = random_int(100000, 999999);

        $payment = Payment::where('cartid', $id)->firstOrFail();
    
        $apiKey = config('services.cardknox.api_key');
    
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://localemv.com:8887',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'xCommand=cc%3Asale&xInvoice=IN.' . 
            urlencode($invoiceRandom) . '&xAmount=' . 
            urlencode($request->xAmount) . '&xKey=' . urlencode($apiKey),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
            CURLOPT_SSL_VERIFYPEER => false, 
            CURLOPT_SSL_VERIFYHOST => false,
        ));
    
        $response = curl_exec($curl);
    
        if ($response === false) {
            return response()->json([
                'error' => curl_error($curl)
            ], 500); 
        }
    
        curl_close($curl);
    
        $payment->payment += $paymentAmount;
        $payment->save();
        
        return response()->json([
            'success' => $response,
            'message' => 'Payment processed and reservation saved successfully.'
        ]);
    }
}
