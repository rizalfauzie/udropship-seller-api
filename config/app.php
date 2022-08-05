<?php

return [
    'name' => env('APP_NAME', 'Seller API'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),

    'timezone' => env('APP_TIMEZONE', 'Asia/Jakarta'),
    'locale' => 'en',
    'fallback_locale' => 'en',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',

    'magento' => [
        'api_url' => env('MAGENTO_API_URL', 'http://localhost/rest/all/V1'),
        'api_token' => env('MAGENTO_TOKEN', '')
    ]
];
