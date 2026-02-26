<?php

return [
    'api_url'  => env('XGATE_API_URL', 'https://api.xgateglobal.com'),
    'email'    => env('XGATE_EMAIL'),
    'password' => env('XGATE_PASSWORD'),

    'webhook_url' => env('APP_URL') . '/api/webhook/xgate',
];
