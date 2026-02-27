<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PesaPalService
{
    protected $consumerKey;
    protected $consumerSecret;
    protected $baseUrl;
    protected $callbackUrl;
    protected $ipnUrl;

    public function __construct()
    {
        $this->consumerKey    = config('pesapal.consumer_key');
        $this->consumerSecret = config('pesapal.consumer_secret');
        $this->baseUrl        = rtrim(config('pesapal.base_url'), '/');
        $this->callbackUrl    = config('pesapal.callback_url');
        $this->ipnUrl         = config('pesapal.ipn_url');
    }

    public function getAccessToken()
    {
        $response = Http::post($this->baseUrl . '/v3/api/Auth/RequestToken', [
            'consumer_key'    => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret,
        ]);

        if (!$response->successful()) {
            Log::error('Token Error', ['body' => $response->body()]);
            return null;
        }

        return $response->json()['token'] ?? null;
    }

    public function registerIPN($token)
    {
        $response = Http::withToken($token)
            ->post($this->baseUrl . '/v3/api/URLSetup/RegisterIPN', [
                'url' => $this->ipnUrl,
                'ipn_notification_type' => 'POST'
            ]);

        if (!$response->successful()) {
            Log::error('IPN Registration Failed', ['body' => $response->body()]);
            return null;
        }

        return $response->json();
    }

    public function submitOrder($token, $orderData)
    {
        $response = Http::withToken($token)
            ->post($this->baseUrl . '/v3/api/Transactions/SubmitOrderRequest', $orderData);

        if (!$response->successful()) {
            Log::error('Submit Order Failed', ['body' => $response->body()]);
            return null;
        }

        return $response->json();
    }

    public function getTransactionStatus($token, $trackingId)
    {
        $response = Http::withToken($token)
            ->get($this->baseUrl . '/v3/api/Transactions/GetTransactionStatus', [
                'orderTrackingId' => $trackingId
            ]);

        if (!$response->successful()) {
            Log::error('Status Check Failed', ['body' => $response->body()]);
            return null;
        }

        return $response->json();
    }

    public function prepareOrderData($payment, $company, $user, $notificationId)
    {
        return [
            'id' => $payment->merchant_reference,
            'currency' => 'TZS',
            'amount' => (int) $payment->amount,
            'description' => 'Package Payment - ' . $payment->package_type,
            'callback_url' => $this->callbackUrl,
            'notification_id' => $notificationId,
            'redirect_mode' => false,

            'billing_address' => [
                'email_address' => $user->email,
                'phone_number'  => $payment->phone_number,
                'country_code'  => 'TZ',
                'first_name'    => $user->name,
                'last_name'     => '',
                'line_1'        => $company->location ?? 'Dar es Salaam',
                'city'          => $company->region ?? 'Dar es Salaam',
                'state'         => $company->region ?? 'Dar es Salaam',
                'postal_code'   => '00000',
                'zip_code'      => '00000'
            ]
        ];
    }
}