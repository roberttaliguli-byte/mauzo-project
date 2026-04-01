<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SMSService
{
    protected $baseUrl;
    protected $username;
    protected $password;
    protected $senderId;

    public function __construct()
    {
        $this->baseUrl = config('sms.base_url', 'https://messaging-service.co.tz');
        $this->username = config('sms.username');
        $this->password = config('sms.password');
        $this->senderId = config('sms.sender_id', 'MAUZO SHEET');
    }

    /**
     * Get current company ID
     */
    private function getCompanyId()
    {
        if (Auth::guard('mfanyakazi')->check()) {
            return Auth::guard('mfanyakazi')->user()->company_id;
        }
        
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user()->company_id;
        }
        
        return null;
    }





    /**
     * Send SMS to single or multiple recipients using GET method
     */
    public function sendSms($to, $message, $reference = null)
    {
        try {
            $companyId = $this->getCompanyId();
            
            // Convert single recipient to array
            $recipients = is_array($to) ? $to : [$to];
            
            $results = [];
            $allSuccessful = true;
            
            // Send one SMS at a time using GET method
            foreach ($recipients as $recipient) {
                // Clean and format phone number
                $phone = preg_replace('/[^0-9]/', '', $recipient);
                if (substr($phone, 0, 1) == '0') {
                    $phone = '255' . substr($phone, 1);
                }
                
                // URL encode parameters
                $params = [
                    'username' => $this->username,
                    'password' => $this->password,
                    'from' => $this->senderId,
                    'to' => $phone,
                    'text' => $message
                ];
                
                $url = $this->baseUrl . "/link/sms/v1/text/single?" . http_build_query($params);
                
                Log::info('Sending SMS via GET', [
                    'url' => $url,
                    'recipient' => $phone
                ]);
                
                $response = Http::withoutVerifying()->get($url);
                
                Log::info('SMS API Response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'recipient' => $phone
                ]);
                
                $responseData = $response->json();
                $isSuccessful = $response->successful() && isset($responseData['messages']);
                
                if (!$isSuccessful) {
                    $allSuccessful = false;
                }
                
                // Get status info
                $status = 'UNKNOWN';
                $statusDescription = '';
                $smsCount = 1;
                
                if ($isSuccessful && isset($responseData['messages'][0])) {
                    $msg = $responseData['messages'][0];
                    $status = $msg['status']['name'] ?? 'SENT';
                    $statusDescription = $msg['status']['description'] ?? '';
                    $smsCount = $msg['smsCount'] ?? 1;
                } else {
                    $status = 'FAILED';
                    $statusDescription = $response->body();
                }
                
                // Log the SMS
                \App\Models\SmsLog::create([
                    'company_id' => $companyId ?: null,
                    'recipient' => $phone,
                    'message' => $message,
                    'status' => $status,
                    'status_description' => $statusDescription,
                    'sms_count' => $smsCount,
                    'reference' => $reference,
                    'sent_at' => now(),
                ]);
                
                $results[] = [
                    'to' => $phone,
                    'success' => $isSuccessful,
                    'status' => $status,
                    'data' => $responseData
                ];
            }
            
            return [
                'success' => $allSuccessful,
                'message' => $allSuccessful ? 'SMS zimetumwa kikamilifu' : 'Baadhi ya SMS zimefanikiwa, zingine zimeshindwa',
                'data' => $results
            ];
            
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Hitilafu ya mtandao: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Test API connection
     */
    public function testConnection()
    {
        try {
            $params = [
                'username' => $this->username,
                'password' => $this->password,
                'from' => $this->senderId,
                'to' => '255712345678',
                'text' => 'Test message from MAUZO system'
            ];
            
            $url = $this->baseUrl . "/link/sms/v1/text/single?" . http_build_query($params);
            
            Log::info('Testing SMS connection', ['url' => $url]);
            
            $response = Http::withoutVerifying()->get($url);
            
            Log::info('SMS Test Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['messages'])) {
                    return [
                        'success' => true,
                        'message' => 'Unganisho la SMS linafanya kazi vizuri!',
                        'data' => $data
                    ];
                }
            }
            
            return [
                'success' => false,
                'message' => 'Hitilafu: ' . $response->body(),
                'data' => null
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Get total SMS sent count
     */
    public function getTotalSmsSent()
    {
        $companyId = $this->getCompanyId();
        return \App\Models\SmsLog::where('company_id', $companyId)->count();
    }

    /**
     * Get SMS sent count for today
     */
    public function getTodaySmsSent()
    {
        $companyId = $this->getCompanyId();
        return \App\Models\SmsLog::where('company_id', $companyId)
            ->whereDate('sent_at', today())
            ->count();
    }

    /**
     * Get SMS sent count for this month
     */
    public function getMonthSmsSent()
    {
        $companyId = $this->getCompanyId();
        return \App\Models\SmsLog::where('company_id', $companyId)
            ->whereMonth('sent_at', now()->month)
            ->whereYear('sent_at', now()->year)
            ->count();
    }
}