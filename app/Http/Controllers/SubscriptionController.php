<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\Company;
use NjoguAmos\Pesapal\Facades\Pesapal;
use NjoguAmos\Pesapal\DTOs\PesapalOrderData;
use NjoguAmos\Pesapal\DTOs\PesapalAddressData;
use NjoguAmos\Pesapal\Enums\ISOCurrencyCode;
use NjoguAmos\Pesapal\Enums\ISOCountryCode;
use NjoguAmos\Pesapal\Enums\RedirectMode;
use NjoguAmos\Pesapal\Models\PesapalToken;

class SubscriptionController extends Controller
{
    // ===============================
    // Blocked page
    // ===============================
    public function blocked()
    {
        $company = auth()->user()->company;
        $status = $company ? $company->subscriptionStatus() : 'none';
        return view('subscription.blocked', compact('company', 'status'));
    }

    // ===============================
    // Choose subscription package
    // ===============================
    public function choose()
    {
        $packages = [
            ['id' => 'm1', 'label' => 'Mwezi 1', 'days' => 30,  'price' => 15000],
            ['id' => 'm6', 'label' => 'Miezi 6', 'days' => 180, 'price' => 75000],
            ['id' => 'y1', 'label' => 'Mwaka 1', 'days' => 365, 'price' => 130000],
        ];

        // Check for pending transactions
        $pendingTransaction = Transaction::where('company_id', auth()->user()->company->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('subscription.choose', compact('packages', 'pendingTransaction'));
    }

    // ===============================
    // Payment status page
    // ===============================
    public function paymentStatus($transactionId = null)
    {
        if ($transactionId) {
            $transaction = Transaction::findOrFail($transactionId);
            
            // Ensure user can only view their own transactions
            if ($transaction->company_id !== auth()->user()->company->id) {
                abort(403);
            }
            
            return view('subscription.payment-status', compact('transaction'));
        }
        
        // If no transaction ID, get latest pending transaction
        $transaction = Transaction::where('company_id', auth()->user()->company->id)
            ->whereIn('status', ['pending', 'failed', 'processing'])
            ->latest()
            ->first();
        
        if (!$transaction) {
            return redirect()->route('package.choose')
                ->with('info', 'No active payment found. Please start a new payment.');
        }
        
        return view('subscription.payment-status', compact('transaction'));
    }

    // ===============================
    // Check payment status manually
    // ===============================
    public function checkPaymentStatus($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);
        
        // Ensure user can only check their own transactions
        if ($transaction->company_id !== auth()->user()->company->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        if ($transaction->status === 'completed') {
            return response()->json([
                'status' => 'completed',
                'message' => 'Payment already completed!',
                'redirect' => route('dashboard')
            ]);
        }
        
        try {
            // If transaction has Pesapal tracking ID, check status
            if ($transaction->pesapal_tracking_id) {
                $result = $this->getPesapalTransactionStatus($transaction->pesapal_tracking_id);
                
                if (($result['status'] ?? null) === 'COMPLETED') {
                    $this->completePayment($transaction, $result);
                    
                    return response()->json([
                        'status' => 'completed',
                        'message' => 'Payment completed successfully!',
                        'redirect' => route('dashboard')
                    ]);
                }
                
                // Update transaction status based on Pesapal response
                $pesapalStatus = strtolower($result['status'] ?? 'pending');
                $transaction->update(['status' => $pesapalStatus]);
                
                $statusMessages = [
                    'pending' => 'Payment is still pending. Please wait...',
                    'processing' => 'Payment is being processed...',
                    'failed' => 'Payment failed. Please try again.',
                    'reversed' => 'Payment was reversed.',
                    'expired' => 'Payment session expired.'
                ];
                
                return response()->json([
                    'status' => $pesapalStatus,
                    'message' => $statusMessages[$pesapalStatus] ?? 'Payment status: ' . $pesapalStatus,
                    'refresh' => true
                ]);
            }
            
            // If no tracking ID yet, transaction is still initializing
            return response()->json([
                'status' => 'pending',
                'message' => 'Payment is being initialized...',
                'refresh' => false
            ]);
            
        } catch (\Exception $e) {
            Log::error("Payment status check error: " . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error checking payment status. Please try again.'
            ]);
        }
    }

    // ===============================
    // Start Pesapal payment
    // ===============================
    public function startPayment(Request $request)
    {
        $request->validate(['package' => 'required']);

        // Check for existing pending transaction
        $existingPending = Transaction::where('company_id', auth()->user()->company->id)
            ->where('status', 'pending')
            ->first();
            
        if ($existingPending) {
            return redirect()->route('package.payment-status', $existingPending->id)
                ->with('info', 'You already have a pending payment. Please complete or cancel it first.');
        }

        $map = [
            'm1' => ['days' => 30,  'price' => 15000,  'label' => 'Mwezi 1'],
            'm6' => ['days' => 180, 'price' => 75000,  'label' => 'Miezi 6'],
            'y1' => ['days' => 365, 'price' => 130000, 'label' => 'Mwaka 1'],
        ];

        if (!isset($map[$request->package])) {
            return redirect()->route('package.choose')
                ->with('error', 'Invalid package selected.');
        }

        $selected = $map[$request->package];

        try {
            // Generate Pesapal token if needed
            $this->ensurePesapalToken();

            // Create transaction
            $transaction = auth()->user()->company->transactions()->create([
                'package' => $selected['label'],
                'amount'  => $selected['price'],
                'status'  => 'pending',
                'payment_method' => 'pesapal',
            ]);

            Log::info("Created pending transaction ID {$transaction->id}");

            // Create Pesapal order
            $orderData = new PesapalOrderData(
                (string)$transaction->id,
                ISOCurrencyCode::TZS,
                $selected['price'],
                "Subscription: {$selected['label']} - " . auth()->user()->company->name,
                route('pesapal.callback'),
                '',
                route('package.payment-status', $transaction->id),
                RedirectMode::PARENT_WINDOW
            );

            $billingAddress = new PesapalAddressData(
                auth()->user()->phone ?? '255' . rand(700000000, 759999999),
                auth()->user()->email,
                ISOCountryCode::TZ,
                auth()->user()->name,
                '',
                auth()->user()->name
            );

            // Submit order to Pesapal
            $response = Pesapal::createOrder($orderData, $billingAddress);

            Log::info("Pesapal order created", [
                'transaction_id' => $transaction->id,
                'pesapal_response' => $response
            ]);

            if (!isset($response['redirect_url'])) {
                $transaction->update(['status' => 'failed', 'failure_reason' => 'No redirect URL from Pesapal']);
                
                return redirect()->route('package.payment-status', $transaction->id)
                    ->with('error', 'Payment gateway error. Please try again.');
            }

            // Update transaction with initial Pesapal data
            $transaction->update([
                'pesapal_order_tracking_id' => $response['order_tracking_id'] ?? null,
                'pesapal_merchant_reference' => $response['merchant_reference'] ?? null,
            ]);

            // Redirect to Pesapal payment page
            return redirect()->away($response['redirect_url']);

        } catch (\Exception $e) {
            Log::error('Payment initiation error: ' . $e->getMessage());
            
            // Create failed transaction record for tracking
            if (isset($transaction)) {
                $transaction->update([
                    'status' => 'failed',
                    'failure_reason' => $e->getMessage()
                ]);
                
                return redirect()->route('package.payment-status', $transaction->id)
                    ->with('error', 'Payment initiation failed: ' . $e->getMessage());
            }
            
            return redirect()->route('package.choose')
                ->with('error', 'Payment initiation failed. Please try again.');
        }
    }

    // ===============================
    // Pesapal callback
    // ===============================
    public function paymentSuccess(Request $request)
    {
        $trackingId  = $request->query('pesapal_transaction_tracking_id');
        $merchantRef = $request->query('pesapal_merchant_reference');
        $orderTrackingId = $request->query('pesapal_order_tracking_id');

        Log::info("Pesapal callback received", $request->all());

        if (!$trackingId || !$merchantRef) {
            return redirect()->route('package.choose')
                ->with('error', 'Invalid payment callback parameters.');
        }

        $transaction = Transaction::where('id', $merchantRef)
            ->orWhere('pesapal_merchant_reference', $merchantRef)
            ->first();

        if (!$transaction) {
            Log::error('Transaction not found for callback', [
                'merchant_ref' => $merchantRef,
                'tracking_id' => $trackingId
            ]);
            
            return redirect()->route('package.choose')
                ->with('error', 'Transaction not found.');
        }

        try {
            // Get transaction status from Pesapal
            $result = $this->getPesapalTransactionStatus($trackingId);
            
            Log::info("Pesapal transaction status check", [
                'transaction_id' => $transaction->id,
                'pesapal_status' => $result['status'] ?? 'unknown',
                'pesapal_response' => $result
            ]);

            $pesapalStatus = strtolower($result['status'] ?? 'pending');
            
            // Update transaction with callback data
            $transaction->update([
                'pesapal_tracking_id' => $trackingId,
                'pesapal_merchant_reference' => $merchantRef,
                'pesapal_order_tracking_id' => $orderTrackingId ?? $transaction->pesapal_order_tracking_id,
                'status' => $pesapalStatus,
                'payment_method' => $result['payment_method'] ?? null,
                'paid_amount' => $result['amount'] ?? null,
                'currency' => $result['currency'] ?? 'TZS',
            ]);

            if ($pesapalStatus === 'completed') {
                // Complete the payment
                $this->completePayment($transaction, $result);
                
                // Redirect to payment status page with success message
                return redirect()->route('package.payment-status', $transaction->id)
                    ->with('success', 'Payment completed successfully! Your subscription has been activated.');
            }
            
            // For other statuses, redirect to payment status page
            $statusMessages = [
                'pending' => 'Payment is pending. Please complete the payment on your mobile device.',
                'processing' => 'Payment is being processed. This may take a few moments.',
                'failed' => 'Payment failed. Please try again.',
                'reversed' => 'Payment was reversed.',
                'expired' => 'Payment session expired.',
            ];
            
            return redirect()->route('package.payment-status', $transaction->id)
                ->with('info', $statusMessages[$pesapalStatus] ?? 'Payment status: ' . $pesapalStatus);

        } catch (\Exception $e) {
            Log::error("Pesapal callback error: " . $e->getMessage());
            
            // Update transaction as error
            $transaction->update([
                'status' => 'error',
                'failure_reason' => $e->getMessage()
            ]);
            
            return redirect()->route('package.payment-status', $transaction->id)
                ->with('error', 'Payment verification error. Please contact support.');
        }
    }

    // ===============================
    // Retry payment
    // ===============================
    public function retryPayment($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);
        
        // Ensure user can only retry their own failed transactions
        if ($transaction->company_id !== auth()->user()->company->id) {
            abort(403);
        }
        
        if (!in_array($transaction->status, ['failed', 'expired'])) {
            return redirect()->route('package.payment-status', $transaction->id)
                ->with('error', 'Only failed or expired payments can be retried.');
        }
        
        try {
            // Generate new Pesapal order
            $this->ensurePesapalToken();
            
            $orderData = new PesapalOrderData(
                (string)$transaction->id,
                ISOCurrencyCode::TZS,
                $transaction->amount,
                "Retry: {$transaction->package} - " . auth()->user()->company->name,
                route('pesapal.callback'),
                '',
                route('package.payment-status', $transaction->id),
                RedirectMode::PARENT_WINDOW
            );
            
            $billingAddress = new PesapalAddressData(
                auth()->user()->phone ?? '255' . rand(700000000, 759999999),
                auth()->user()->email,
                ISOCountryCode::TZ,
                auth()->user()->name,
                '',
                auth()->user()->name
            );
            
            $response = Pesapal::createOrder($orderData, $billingAddress);
            
            if (!isset($response['redirect_url'])) {
                return redirect()->route('package.payment-status', $transaction->id)
                    ->with('error', 'Failed to create payment retry. Please try again.');
            }
            
            // Update transaction for retry
            $transaction->update([
                'status' => 'pending',
                'retry_count' => $transaction->retry_count + 1,
                'pesapal_order_tracking_id' => $response['order_tracking_id'] ?? null,
                'failure_reason' => null,
            ]);
            
            return redirect()->away($response['redirect_url']);
            
        } catch (\Exception $e) {
            Log::error('Payment retry error: ' . $e->getMessage());
            
            return redirect()->route('package.payment-status', $transaction->id)
                ->with('error', 'Retry failed: ' . $e->getMessage());
        }
    }

    // ===============================
    // Cancel payment
    // ===============================
    public function cancelPayment($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);
        
        // Ensure user can only cancel their own pending transactions
        if ($transaction->company_id !== auth()->user()->company->id) {
            abort(403);
        }
        
        if (!in_array($transaction->status, ['pending', 'processing'])) {
            return redirect()->route('package.payment-status', $transaction->id)
                ->with('error', 'Only pending payments can be cancelled.');
        }
        
        $transaction->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
        
        return redirect()->route('package.choose')
            ->with('success', 'Payment cancelled successfully.');
    }

    // ===============================
    // Helper Methods
    // ===============================
    
    /**
     * Ensure Pesapal token exists and is valid
     */
    private function ensurePesapalToken()
    {
        $token = PesapalToken::latest()->first();
        
        if (!$token || $token->expires_at->isPast()) {
            $this->generatePesapalToken();
        }
    }
    
    /**
     * Generate and store Pesapal access token
     */
    private function generatePesapalToken()
    {
        $consumerKey = config('pesapal.consumer_key');
        $consumerSecret = config('pesapal.consumer_secret');
        $environment = config('pesapal.environment', 'sandbox');
        
        $baseUrl = $environment === 'live' 
            ? 'https://pay.pesapal.com/v3' 
            : 'https://cybqa.pesapal.com/pesapalv3';
        
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post($baseUrl . '/api/Auth/RequestToken', [
            'consumer_key' => $consumerKey,
            'consumer_secret' => $consumerSecret,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get Pesapal token: ' . $response->body());
        }

        $data = $response->json();
        
        // Delete old tokens
        PesapalToken::truncate();
        
        // Store new token
        PesapalToken::create([
            'access_token' => $data['token'],
            'expires_at' => now()->addMinutes(30),
        ]);
        
        Log::info('Pesapal token generated successfully');
    }
    
    /**
     * Get transaction status from Pesapal
     */
    private function getPesapalTransactionStatus($trackingId)
    {
        $this->ensurePesapalToken();
        return Pesapal::getTransactionStatus($trackingId);
    }
    
    /**
     * Complete payment and activate subscription
     */
    private function completePayment($transaction, $pesapalData)
    {
        // Update transaction
        $transaction->update([
            'status' => 'completed',
            'paid_amount' => $pesapalData['amount'] ?? $transaction->amount,
            'currency' => $pesapalData['currency'] ?? 'TZS',
            'payment_method' => $pesapalData['payment_method'] ?? 'pesapal',
            'payment_date' => now(),
            'confirmation_code' => $pesapalData['confirmation_code'] ?? null,
        ]);
        
        // Activate company subscription
        $company = Company::find($transaction->company_id);
        if ($company) {
            $company->update([
                'package' => $transaction->package,
                'package_start' => now(),
                'package_end' => now()->addDays($this->getPackageDays($transaction->package)),
                'subscription_active' => true,
            ]);
            
            Log::info("Subscription activated for company {$company->id}", [
                'transaction_id' => $transaction->id,
                'package' => $transaction->package,
                'package_end' => $company->package_end
            ]);
        }
    }
    
    /**
     * Get days for package label
     */
    private function getPackageDays($packageLabel)
    {
        $packages = [
            'Mwezi 1' => 30,
            'Miezi 6' => 180,
            'Mwaka 1' => 365,
        ];
        
        return $packages[$packageLabel] ?? 30;
    }
}