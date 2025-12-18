<?php
// app/Console/Commands/GeneratePesapalToken.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use NjoguAmos\Pesapal\Models\PesapalToken;

class GeneratePesapalToken extends Command
{
    protected $signature = 'pesapal:generate-token';
    protected $description = 'Generate and store Pesapal access token';

    public function handle()
    {
        $consumerKey = config('pesapal.consumer_key');
        $consumerSecret = config('pesapal.consumer_secret');
        $environment = config('pesapal.environment', 'sandbox');
        
        $baseUrl = $environment === 'live' 
            ? 'https://pay.pesapal.com/v3' 
            : 'https://cybqa.pesapal.com/pesapalv3';
        
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/api/Auth/RequestToken', [
                'consumer_key' => $consumerKey,
                'consumer_secret' => $consumerSecret,
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Delete old tokens
                PesapalToken::truncate();
                
                // Store new token
                PesapalToken::create([
                    'access_token' => $data['token'],
                    'expires_at' => now()->addMinutes(30),
                ]);
                
                $this->info('✅ Pesapal token generated successfully!');
                $this->info('Token: ' . substr($data['token'], 0, 50) . '...');
            } else {
                $this->error('❌ Failed to generate token: ' . $response->body());
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}