<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AbacatePay API Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações da API do AbacatePay para processamento de pagamentos PIX
    |
    */

    'api_url' => env('ABACATEPAY_API_URL', 'https://api.abacatepay.com/v1'),
    'api_key' => env('ABACATEPAY_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    */

    'webhook_url' => env('ABACATEPAY_WEBHOOK_URL') . '/api/webhook/abacatepay',
];
