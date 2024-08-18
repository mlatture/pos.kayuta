<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class BaseService {

    private Client $client;
    private array $headers;

    public function __construct(string $baseUrl, ?array $headers = null){
        if($headers) {
            $this->headers = $headers;
        }
        else{
            $this->headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];
        }
        $this->client = new Client([
            'base_uri' => $baseUrl
        ]);
    }

    /**
     * Make a POST Request
     * @param string $endpoint
     * @param mixed|null $data
     * @param array|null $headers
     * @return array
     * @throws GuzzleException
     */
    protected function postRequest(string $endpoint, mixed $data = null, ?array $headers = null, ?bool $json = true): array
    {
        try{
            $response = $this->client->post($endpoint,[
                'headers' => $headers ?: $this->headers,
                'body' => $data
            ]);
            if($json) {
                return [
                    'success' => true,
                    'data' => json_decode($response->getBody(),true)
                ];
            }
            else {
                $resultData = [];
                parse_str($response->getBody(),$resultData);
                return [
                    'success' => true,
                    'data' => $resultData
                ];
            }
        }
        catch (ClientException $e) {
            return [
                'success' => false,
                'data' => json_decode($e->getResponse()->getBody(),true)
            ];
        }
    }

    /**
     * Make a multipart POST Request
     * @param string $endpoint
     * @param array $multipartData
     * @param array|null $headers
     * @return array
     * @throws GuzzleException
     */
    protected function postRequestMultipart(string $endpoint, array $multipartData, ?array $headers = null): array
    {
        $headersToSend = $headers ?: $this->headers;
        unset($headersToSend['Content-Type']);
        try{
            $response = $this->client->post($endpoint,[
                'headers' => $headersToSend,
                'multipart' => $multipartData
            ]);
            return [
                'success' => true,
                'data' => json_decode($response->getBody(),true)
            ];
        }
        catch (ClientException $e) {
            return [
                'success' => false,
                'data' => json_decode($e->getResponse()->getBody(),true)
            ];
        }
    }

    /**
     * Make a GET Request
     * @param string $endpoint
     * @param array|null $headers
     * @return array
     * @throws GuzzleException
     */
    protected function getRequest(string $endpoint, ?array $headers = null): array
    {
        try{
            $response = $this->client->get($endpoint,[
                'headers' => $headers ?: $this->headers,
            ]);
            return [
                'success' => true,
                'data' => json_decode($response->getBody(),true)
            ];
        }
        catch (ClientException $e) {
            return [
                'success' => false,
                'data' => json_decode($e->getResponse()->getBody(),true)
            ];
        }
    }

    /**
     * Override Default Headers
     * @param array $headers
     * @return void
     */
    protected function setDefaultHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

}
