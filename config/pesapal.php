<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    | Set the environment you want to use.
    | Available options: sandbox, live
    */
    'environment' => env('PESAPAL_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Consumer Key
    |--------------------------------------------------------------------------
    | The consumer key obtained from the Pesapal dashboard.
    */
    'consumer_key' => env('PESAPAL_CONSUMER_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Consumer Secret
    |--------------------------------------------------------------------------
    | The consumer secret obtained from the Pesapal dashboard.
    */
    'consumer_secret' => env('PESAPAL_CONSUMER_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Callback URL
    |--------------------------------------------------------------------------
    | The URL where Pesapal will redirect after payment.
    */
    'callback_url' => env('PESAPAL_CALLBACK_URL'),
];