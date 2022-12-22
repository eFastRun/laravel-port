<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'baseUrl' => 'https://pro-api.coinmarketcap.com',

    'apis' => [
        'listing' => '/v1/cryptocurrency/listings/latest',
        'quotes' => '/v1/cryptocurrency/quotes/latest'
    ],

    'api_key' => "4e3fcaec-c886-49e0-8969-0713d46a1bfa",

    'coin' => ['btc' => 1, 'eth' => 1027, 'usdt' => 825, 'usdc' => 3408],

];
