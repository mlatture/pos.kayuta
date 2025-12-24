<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\CardsOnFile;
class CardKnoxService
{
    protected string $endpoint;
    protected string $apiKey;

    public function __construct()
    {
        $this->endpoint = config('services.cardknox.endpoint', 'https://x1.cardknox.com/gateway');
        $this->apiKey = config('services.cardknox.api_key');
    }

    /**
     * Process a credit card sale.
     *
     * @param string $cardNumber
     * @param string $expiry Format: MM/YY or MMYY
     * @param float $amount
     * @return array Parsed response from Cardknox
     */
    public function sale(string $cardNumber, string $cvv, string $expiry, float $amount, string $name, string $email): array
    {
        $xExp = str_replace('/', '', $expiry);

        $data = [
            'xKey' => $this->apiKey,
            'xCommand' => 'cc:sale',
            'xVersion' => '5.0.0',
            'xCardNum' => $cardNumber,
            'xCVV' => $cvv,
            'xExp' => $xExp,
            'xAmount' => number_format($amount, 2, '.', ''),
            'xSoftwareName' => 'KayutaLake',
            'xSoftwareVersion' => '1.0',
            'xAllowDuplicate' => 'true',
        ];

        $response = $this->send($data);

        // Optionally store card on file
        if (isset($response['xToken'], $response['xMaskedCardNumber'])) {
            CardsOnFile::updateOrCreate(
                ['xtoken' => $response['xToken']],
                [
                    'xmaskedcardnumber' => $response['xMaskedCardNumber'],
                    'method' => $response['xCardType'] ?? 'Unknown',
                    'xtoken' => $response['xToken'],
                    'name' => $name ?? null,
                    'email' => $email ?? null,
                    // 'receipt' => $response['xRefNum'] ?? null,
                    'gateway_response' => json_encode($response),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }

        return $response;
    }

    public function saveSale(string $xToken, string $cvv, float $amount, string $name, string $email): array
    {
        $data = [
            'xKey' => $this->apiKey,
            'xCommand' => 'cc:sale',
            'xVersion' => '5.0.0',
            'xToken' => $xToken,
            'xCVV' => $cvv,
            'xAmount' => number_format($amount, 2, '.', ''),
            'xSoftwareName' => 'KayutaLake',
            'xSoftwareVersion' => '1.0',
            'xAllowDuplicate' => 'true',
        ];

        $response = $this->send($data);

        \Log::info('Cardknox cc:sale using xToken', [
            'email' => $email,
            'token' => $xToken,
            'amount' => $amount,
            'response' => $response,
        ]);

        return [
            'success' => $response['xResult'] === 'A',
            'message' => $response['xError'] ?? null,
            'data' => $response,
        ];
    }

    /**
     * Process an ACH sale.
     *
     * @param string $routingNumber
     * @param string $accountNumber
     * @param float $amount
     * @return array Parsed response from Cardknox
     */
    public function achSale(string $routingNumber, string $accountNumber, string $accountName, float $amount, string $name, string $email): array
    {
        $data = [
            'xKey' => $this->apiKey,
            'xCommand' => 'check:sale',
            'xVersion' => '5.0.0',
            'xRouting' => $routingNumber,
            'xAccount' => $accountNumber,
            'xName' => $accountName,
            'xAmount' => number_format($amount, 2, '.', ''),
            'xSoftwareName' => 'KayutaLake',
            'xSoftwareVersion' => '1.0',
            'xAllowDuplicate' => 'true',
        ];

        $response = $this->send($data);

        // Store ACH token if available
        if (isset($response['xToken'])) {
            CardsOnFile::updateOrCreate(
                ['xtoken' => $response['xToken']],
                [
                    'method' => 'ach',
                    'xtoken' => $response['xToken'],
                    'name' => $name ?? null,
                    'email' => $email ?? null,
                    // 'receipt' => $response['xRefNum'] ?? null,
                    'gateway_response' => json_encode($response),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }

        return $response;
    }

    public function refund(string $xRefNum, float $amount, string $reason): array
    {
        $data = [
            'xKey' => $this->apiKey,
            'xCommand' => 'cc:refund',
            'xVersion' => '5.0.0',
            'xRefNum' => $xRefNum,
            'xAmount' => number_format($amount, 2, '.', ''),
            'xSoftwareName' => 'KayutaLake',
            'xSoftwareVersion' => '1.0',
            'xAllowDuplicate' => 'true',
            'xDescription' => $reason,
        ];

        $response = $this->send($data);

        \Log::info('Cardknox cc:refund', [
            'ref' => $xRefNum,
            'amount' => $amount,
            'response' => $response,
        ]);

        return $response;
    }

    /**
     * Send the request to Cardknox and parse response.
     *
     * @param array $data
     * @return array
     */
    protected function send(array $data): array
    {
        $response = Http::asForm()
            ->withHeaders([
                'X-Recurring-Api-Version' => '1.0',
                'Accept' => 'application/json',
            ])
            ->post($this->endpoint, $data);

        $body = $response->body();
        parse_str($body, $parsed);

        return $parsed;
    }
}
