<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

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
    public function sale(string $cardNumber, string $expiry, float $amount): array
    {
        $xExp = str_replace('/', '', $expiry);

        $data = [
            'xKey' => $this->apiKey,
            'xCommand' => 'cc:sale',
            'xVersion' => '5.0.0',
            'xCardNum' => $cardNumber,
            'xExp' => $xExp,
            'xAmount' => number_format($amount, 2, '.', ''),
            'xSoftwareName' => 'KayutaLake',
            'xSoftwareVersion' => '1.0',
            'xAllowDuplicate' => 'true',
        ];

        return $this->send($data);
    }

    /**
     * Process an ACH sale.
     *
     * @param string $routingNumber
     * @param string $accountNumber
     * @param float $amount
     * @return array Parsed response from Cardknox
     */
    public function achSale(string $routingNumber, string $accountNumber, string $accountName,float $amount): array
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

        return $this->send($data);
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
