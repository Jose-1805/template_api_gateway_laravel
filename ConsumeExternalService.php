<?php

namespace App\Traits;

use Exception;
use GuzzleHttp\Client;

trait ConsumeExternalService
{
    /**
     * Solicitud http a un servicio del cluster
     * @param $method
     * @param $requestUrl
     * @param array $formParams
     * @param array $headers
     * @return array
     */
    public function performRequest($method, $requestUrl, $formParams = [], $headers = []): array
    {
        $client = new Client([
            'base_uri'  =>  $this->base_uri,
        ]);

        if (isset($this->secret)) {
            $headers['Authorization'] = $this->secret;
        }

        $response = $client->request($method, $requestUrl, [
            'form_params' => $formParams,
            "http_errors" => false,
            'headers'     => $headers,
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if ($data == null) {
            $data = ['error' => strlen($response->getBody()->getContents()) ? $response->getBody()->getContents() : "Error interno del servidor", 'code' => 500];
        }

        return $data;
    }
}
