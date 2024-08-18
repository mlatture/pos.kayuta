<?php

namespace App\Services;

class CardKnoxService extends BaseService {

    public function __construct(){
        parent::__construct('https://x1.cardknox.com', [
            'Content-type: application/x-www-form-urlencoded',
            'X-Recurring-Api-Version: 1.0', // Replace with the appropriate API version if needed.
            'Accept' => 'application/json'
        ]);
    }

    /**
     * Create Sale
     * @param $cardNumber
     * @param $amount
     * @param $expiry
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sale($cardNumber, $amount, $expiry): array
    {
        $xExp = str_replace('/','',$expiry);
        $data = [
            'xKey' => config('services.cardknox.api_key'),
            // 'api-secret' => config('services.cardknox.api_secret'),
            "xVersion" => "4.5.5",
            "xCommand" => "cc:Sale",
            'xAmount' => $amount,
            'xCardNum' => $cardNumber,
//            'xCVV' => '123',
            'xExp'     => $xExp,
            'xSoftwareVersion' => '1.0',
            'xSoftwareName' => 'KayutaLake'
        ];
        return $this->postRequest('/gateway',http_build_query($data),null,false);
    }

}
