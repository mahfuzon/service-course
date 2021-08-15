<?php

use Illuminate\Support\Facades\Http;

function postOrder($params)
{
    $url = env('SERVICE_ORDER_PAYMENT_URL') . 'api/orders';
    try {
        $response = Http::post($url, $params);
        $data = $response->json();
        $data['http_code'] = $response->status();
        return $data;
    } catch (\Throwable $th) {
        return [
            'status' => 'error',
            'http_code' => 500,
            'message' => 'service order payment unavailable'
        ];
    }
}
