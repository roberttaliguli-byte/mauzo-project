<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\SmsLog;

class SMSService
{
    protected $baseUrl;
    protected $token;
    protected $senderId;

    public function __construct()
    {
        $this->baseUrl = config('sms.base_url');
        $this->token = config('sms.token');
        $this->senderId = config('sms.sender_id');
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
     * Format Tanzania phone number
     */
    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // 07XXXXXXXX -> 2557XXXXXXXX
        if (substr($phone, 0, 1) == '0') {
            $phone = '255' . substr($phone, 1);
        }

        // Remove +
        $phone = str_replace('+', '', $phone);

        return $phone;
    }

    /**
     * Send SMS
     */
    public function sendSms($to, $message, $reference = null)
    {
        try {

            $companyId = $this->getCompanyId();

            $recipients = is_array($to) ? $to : [$to];

            $formattedRecipients = [];

            foreach ($recipients as $recipient) {
                $formattedRecipients[] = $this->formatPhone($recipient);
            }

            $payload = [
                'from' => $this->senderId,

                'to' => $formattedRecipients,

                'text' => $message,

                'reference' => $reference ?? uniqid('SMS_'),
            ];

            Log::info('Sending Internet SMS', [
                'payload' => $payload
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post(
                $this->baseUrl . '/api/sms/v2/text/single',
                $payload
            );

            $responseData = $response->json();

            Log::info('Internet SMS API Response', [
                'status' => $response->status(),
                'response' => $responseData
            ]);

            // SUCCESS
            if (
                $response->successful() &&
                isset($responseData['messages'])
            ) {

                foreach ($responseData['messages'] as $sms) {

                    SmsLog::create([
                        'company_id' => $companyId,
                        'recipient' => $sms['to'] ?? null,
                        'message' => $sms['message'] ?? $message,
                        'status' => $sms['status']['name'] ?? 'SENT',
                        'status_description' => $sms['status']['description'] ?? '',
                        'sms_count' => $sms['smsCount'] ?? 1,
                        'reference' => $reference,
                        'sent_at' => now(),
                    ]);
                }

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'data' => $responseData
                ];
            }

            // FAILED
            return [
                'success' => false,
                'message' => 'SMS sending failed',
                'data' => $responseData
            ];

        } catch (\Exception $e) {

            Log::error('Internet SMS Error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Quick single SMS
     */
    public function sendSingle($phone, $message)
    {
        return $this->sendSms($phone, $message);
    }

    /**
     * Test SMS connection
     */
    public function testConnection()
    {
        return $this->sendSms(
            '0712345678',
            'Test SMS from MauzoSheetAI',
            'TEST_SMS'
        );
    }

    /**
     * Total SMS sent
     */
    public function getTotalSmsSent()
    {
        $companyId = $this->getCompanyId();

        return SmsLog::where('company_id', $companyId)
            ->count();
    }

    /**
     * Today SMS sent
     */
    public function getTodaySmsSent()
    {
        $companyId = $this->getCompanyId();

        return SmsLog::where('company_id', $companyId)
            ->whereDate('sent_at', today())
            ->count();
    }

    /**
     * This month SMS sent
     */
    public function getMonthSmsSent()
    {
        $companyId = $this->getCompanyId();

        return SmsLog::where('company_id', $companyId)
            ->whereMonth('sent_at', now()->month)
            ->whereYear('sent_at', now()->year)
            ->count();
    }
}