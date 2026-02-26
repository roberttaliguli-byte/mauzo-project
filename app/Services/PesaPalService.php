<?php
// app/Services/PesaPalService.php

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
    protected $environment;
    protected $liveIpnId;

    public function __construct()
    {
        $this->consumerKey = config('pesapal.consumer_key');
        $this->consumerSecret = config('pesapal.consumer_secret');
        $this->baseUrl = config('pesapal.base_url');
        $this->callbackUrl = config('pesapal.callback_url');
        $this->ipnUrl = config('pesapal.ipn_url');
        $this->environment = config('pesapal.env', 'production');
        $this->liveIpnId = config('pesapal.live_ipn_id');
        
        Log::info('PesaPal Service initialized', [
            'base_url' => $this->baseUrl,
            'environment' => $this->environment,
            'callback_url' => $this->callbackUrl
        ]);
    }

    public function getAccessToken()
    {
        try {
            $url = $this->baseUrl . '/v3/api/Auth/RequestToken';
            
            Log::info('Requesting PesaPal token from: ' . $url);
            
            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(30)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->post($url, [
                'consumer_key' => $this->consumerKey,
                'consumer_secret' => $this->consumerSecret
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('PesaPal token received successfully');
                return $data['token'] ?? null;
            }

            Log::error('PesaPal token error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('PesaPal token exception: ' . $e->getMessage());
            return null;
        }
    }

    public function registerIPN($token)
    {
        try {
            $url = $this->baseUrl . '/v3/api/URLSetup/RegisterIPN';
            
            Log::info('Registering IPN URL at: ' . $url);
            
            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(30)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ])->post($url, [
                'url' => $this->ipnUrl,
                'ipn_notification_type' => 'GET'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('IPN registered successfully', $data);
                return $data;
            }

            Log::error('PesaPal IPN registration error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('PesaPal IPN exception: ' . $e->getMessage());
            return null;
        }
    }

    public function submitOrder($token, $orderData)
    {
        try {
            $url = $this->baseUrl . '/v3/api/Transactions/SubmitOrderRequest';
            
            Log::info('Submitting order to: ' . $url);
            
            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(30)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ])->post($url, $orderData);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Order submitted successfully', $data);
                return $data;
            }

            Log::error('PesaPal order submission error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('PesaPal order exception: ' . $e->getMessage());
            return null;
        }
    }

    public function getTransactionStatus($token, $orderTrackingId)
    {
        try {
            $url = $this->baseUrl . '/v3/api/Transactions/GetTransactionStatus';
            
            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(30)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ])->get($url, [
                'orderTrackingId' => $orderTrackingId
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PesaPal status check error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('PesaPal status exception: ' . $e->getMessage());
            return null;
        }
    }

    public function prepareOrderData($payment, $company, $user)
    {
        // Use live IPN ID for production, fallback to sandbox ID for testing
        $notificationId = $this->environment === 'production' 
            ? $this->liveIpnId 
            : config('pesapal.ipn_id', '3ac40066-bd8c-4613-963b-dab051899923');
            
        return [
            'id' => $payment->merchant_reference,
            'currency' => 'TZS',
            'amount' => $payment->amount,
            'description' => 'Package Payment: ' . $payment->package_type . ' for ' . $company->company_name,
            'callback_url' => $this->callbackUrl,
            'notification_id' => $notificationId,
            'branch' => 'Main Branch',
            'billing_address' => [
                'email_address' => $user->email,
                'phone_number' => $payment->phone_number,
                'country_code' => 'TZ',
                'first_name' => $user->name,
                'middle_name' => '',
                'last_name' => '',
                'line_1' => $company->location ?? 'Dar es Salaam',
                'line_2' => '',
                'city' => $company->region ?? 'Dar es Salaam',
                'state' => $company->region ?? 'Dar es Salaam',
                'postal_code' => '00000',
                'zip_code' => '00000'
            ]
        ];
    }
}