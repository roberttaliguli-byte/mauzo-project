<?php
// config/pesapal.php

return [
    'env' => env('PESAPAL_ENV', 'sandbox'),

    'consumer_key' => env('PESAPAL_CONSUMER_KEY'),

    'consumer_secret' => env('PESAPAL_CONSUMER_SECRET'),

    'base_url' => env('PESAPAL_BASE_URL', 'https://cybqa.pesapal.com/pesapalv3'),

    'callback_url' => env('PESAPAL_CALLBACK_URL', 'https://www.mauzosheetai.co.tz/pesapal/callback'),

    'ipn_url' => env('PESAPAL_IPN_URL', 'https://www.mauzosheetai.co.tz/pesapal/ipn'),
    
    'live_ipn_id' => env('PESAPAL_LIVE_IPN_ID'), // Add this line
];