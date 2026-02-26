<?php
// app/Http/Controllers/PaymentController.php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Payment;
use App\Models\User;
use App\Services\PesaPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentController extends Controller
{
    protected $pesapalService;

    public function __construct(PesaPalService $pesapalService)
    {
        $this->pesapalService = $pesapalService;
    }

    /**
     * Show package selection page when package expired
     */
    public function showPackageSelection()
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('dashboard')->with('error', 'Company not found');
        }

        // Check if package is expired
        $isExpired = $company->package_end && Carbon::parse($company->package_end)->isPast();
        
        // If not expired, redirect to dashboard
        if (!$isExpired && $company->package_end) {
            return redirect()->route('dashboard')->with('info', 'Your package is still active');
        }

        // Updated packages with correct pricing
        $packages = [
            '30 days' => [
                'price' => 15000,
                'days' => 30,
                'description' => 'Monthly subscription - TZS 15,000',
                'badge' => null
            ],
            '180 days' => [
                'price' => 75000,
                'days' => 180,
                'description' => '6 months subscription - Save TZS 15,000 (Regular: TZS 90,000)',
                'badge' => 'Save 16%'
            ],
            '366 days' => [
                'price' => 150000,
                'days' => 366,
                'description' => '1 year subscription - Save TZS 30,000 (Regular: TZS 180,000)',
                'badge' => 'Best Value'
            ]
        ];

        return view('payments.package-selection', compact('company', 'packages', 'isExpired'));
    }

    /**
     * Show payment form for selected package
     */
    public function showPaymentForm(Request $request)
    {
        // Handle GET request
        if ($request->isMethod('get')) {
            $package = $request->query('package');
            
            // If no package in query string, check session
            if (!$package) {
                $package = session('selected_package');
            }
            
            // If still no package, redirect to package selection
            if (!$package) {
                return redirect()->route('payment.package.selection')
                    ->with('error', 'Tafadhali chagua kifurushi kwanza.');
            }
            
            // Validate package - REMOVED FREE TRIAL FROM VALIDATION
            if (!in_array($package, ['30 days', '180 days', '366 days'])) {
                return redirect()->route('payment.package.selection')
                    ->with('error', 'Kifurushi si sahihi.');
            }
        } else {
            // Handle POST request (normal flow)
            $request->validate([
                'package' => 'required|in:30 days,180 days,366 days' // Removed Free Trial
            ]);
            $package = $request->package;
        }

        $user = Auth::user();
        $company = $user->company;
        $amount = Payment::getPackageAmount($package);

        // Store package in session for GET requests
        session(['selected_package' => $package]);

        return view('payments.payment-form', compact('company', 'package', 'amount'));
    }

    /**
     * Process payment with PesaPal
     */
    public function processPayment(Request $request)
    {
        Log::info('Payment process started', [
            'user_id' => Auth::id(),
            'package' => $request->package,
            'phone' => $request->phone_number
        ]);

        $request->validate([
            'package' => 'required|in:30 days,180 days,366 days', // Updated validation
            'phone_number' => 'required|string|max:15',
            'payment_method' => 'required|in:TIGO,VODACOM,AIRTEL'
        ]);

        $user = Auth::user();
        $company = $user->company;
        $package = $request->package;
        $amount = Payment::getPackageAmount($package);
        $phoneNumber = $this->formatPhoneNumber($request->phone_number);

        Log::info('Payment validation passed', [
            'amount' => $amount,
            'formatted_phone' => $phoneNumber
        ]);

        DB::beginTransaction();

        try {
            // Create payment record
            $payment = Payment::create([
                'company_id' => $company->id,
                'transaction_reference' => Payment::generateTransactionReference(),
                'merchant_reference' => Payment::generateMerchantReference(),
                'package_type' => $package,
                'amount' => $amount,
                'currency' => 'TZS',
                'phone_number' => $phoneNumber,
                'payment_method' => $request->payment_method,
                'status' => 'pending'
            ]);

            Log::info('Payment record created', ['payment_id' => $payment->id]);

            // Get PesaPal token
            Log::info('Requesting PesaPal token...');
            $token = $this->pesapalService->getAccessToken();
            
            if (!$token) {
                throw new \Exception('Failed to get payment token');
            }

            Log::info('PesaPal token received');

            // Register IPN if needed
            Log::info('Registering IPN...');
            $ipnResponse = $this->pesapalService->registerIPN($token);
            Log::info('IPN response', ['ipn_response' => $ipnResponse]);
            
            // Prepare order data
            $orderData = $this->pesapalService->prepareOrderData($payment, $company, $user);
            
            // Update with actual notification ID if you got it from registerIPN
            if ($ipnResponse && isset($ipnResponse['ipn_id'])) {
                $orderData['notification_id'] = $ipnResponse['ipn_id'];
            }

            Log::info('Submitting order to PesaPal...');
            // Submit order to PesaPal
            $orderResponse = $this->pesapalService->submitOrder($token, $orderData);

            Log::info('PesaPal order response', ['response' => $orderResponse]);

            if (!$orderResponse || !isset($orderResponse['order_tracking_id'])) {
                throw new \Exception('Failed to submit order to PesaPal: ' . json_encode($orderResponse));
            }

            // Update payment with tracking ID
            $payment->update([
                'pesapal_transaction_tracking_id' => $orderResponse['order_tracking_id'],
                'payment_response_data' => $orderResponse
            ]);

            DB::commit();

            Log::info('Payment process completed successfully', [
                'tracking_id' => $orderResponse['order_tracking_id']
            ]);

            // For mobile money, show PIN prompt instructions with ngrok header
            return response()
                ->view('payments.payment-prompt', [
                    'payment' => $payment,
                    'orderResponse' => $orderResponse
                ])
                ->header('ngrok-skip-browser-warning', 'true');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Payment processing failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * PesaPal callback URL
     */
    public function callback(Request $request)
    {
        Log::info('PesaPal callback received', $request->all());

        $orderTrackingId = $request->input('OrderTrackingId');
        $orderMerchantReference = $request->input('OrderMerchantReference');

        if (!$orderTrackingId) {
            return redirect()->route('payment.failed')
                ->with('error', 'Invalid callback response')
                ->header('ngrok-skip-browser-warning', 'true');
        }

        // Find payment
        $payment = Payment::where('pesapal_transaction_tracking_id', $orderTrackingId)
            ->orWhere('merchant_reference', $orderMerchantReference)
            ->first();

        if (!$payment) {
            Log::error('Payment not found for callback', $request->all());
            return redirect()->route('payment.failed')
                ->with('error', 'Payment record not found')
                ->header('ngrok-skip-browser-warning', 'true');
        }

        // Get transaction status
        $token = $this->pesapalService->getAccessToken();
        $statusResponse = $this->pesapalService->getTransactionStatus($token, $orderTrackingId);

        if ($statusResponse && isset($statusResponse['status_code'])) {
            $payment->update([
                'payment_response_data' => array_merge($payment->payment_response_data ?? [], [
                    'callback' => $request->all(), 
                    'status_check' => $statusResponse
                ]),
                'status' => $this->mapPesaPalStatus($statusResponse['status_code'])
            ]);

            if ($payment->status === 'completed') {
                $this->activatePackage($payment);
                
                return redirect()->route('payment.success', ['reference' => $payment->transaction_reference])
                    ->with('success', 'Payment completed successfully! Your package has been activated.')
                    ->header('ngrok-skip-browser-warning', 'true');
            }
        }
        
        return redirect()->route('payment.status', ['reference' => $payment->transaction_reference])
            ->header('ngrok-skip-browser-warning', 'true');
    }

    /**
     * IPN (Instant Payment Notification) handler
     */
    public function ipn(Request $request)
    {
        Log::info('PesaPal IPN received', $request->all());

        $orderTrackingId = $request->input('OrderTrackingId');
        $orderMerchantReference = $request->input('OrderMerchantReference');

        if (!$orderTrackingId) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $payment = Payment::where('pesapal_transaction_tracking_id', $orderTrackingId)
            ->orWhere('merchant_reference', $orderMerchantReference)
            ->first();

        if (!$payment) {
            Log::error('Payment not found for IPN', $request->all());
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Get transaction status
        $token = $this->pesapalService->getAccessToken();
        $statusResponse = $this->pesapalService->getTransactionStatus($token, $orderTrackingId);

        if ($statusResponse && isset($statusResponse['status_code'])) {
            $payment->update([
                'ipn_data' => $request->all(),
                'payment_response_data' => array_merge($payment->payment_response_data ?? [], [
                    'ipn_status' => $statusResponse
                ]),
                'status' => $this->mapPesaPalStatus($statusResponse['status_code'])
            ]);

            // If payment completed, activate package
            if ($payment->status === 'completed') {
                $this->activatePackage($payment);
            }
        }

        return response()->json(['status' => 'received']);
    }

    /**
     * Show payment success page
     */
    public function paymentSuccess(Request $request, $reference)
    {
        $payment = Payment::where('transaction_reference', $reference)->firstOrFail();
        
        return response()
            ->view('payments.success', compact('payment'))
            ->header('ngrok-skip-browser-warning', 'true');
    }

    /**
     * Show payment failed page
     */
    public function paymentFailed(Request $request)
    {
        return response()
            ->view('payments.failed')
            ->header('ngrok-skip-browser-warning', 'true');
    }

    /**
     * Show payment status
     */
    public function paymentStatus(Request $request, $reference)
    {
        $payment = Payment::where('transaction_reference', $reference)->firstOrFail();
        
        return response()
            ->view('payments.status', compact('payment'))
            ->header('ngrok-skip-browser-warning', 'true');
    }

    /**
     * Activate package after successful payment
     */
    protected function activatePackage($payment)
    {
        $company = $payment->company;
        $start_date = Carbon::now();
        
        switch($payment->package_type) {
            case '30 days':
                $end_date = $start_date->copy()->addDays(30);
                break;
            case '180 days':
                $end_date = $start_date->copy()->addDays(180);
                break;
            case '366 days':
                $end_date = $start_date->copy()->addDays(366);
                break;
            default:
                $end_date = $start_date->copy()->addDays(30);
        }

        // Update company package
        $company->package = $payment->package_type;
        $company->package_start = $start_date;
        $company->package_end = $end_date;
        $company->save();

        // Update payment with expiry date
        $payment->update([
            'payment_date' => now(),
            'expiry_date' => $end_date
        ]);

        Log::info('Package activated', [
            'company_id' => $company->id,
            'package' => $payment->package_type,
            'expiry' => $end_date
        ]);
    }

    /**
     * Format phone number to international format
     */
    protected function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 0, replace with 255
        if (substr($phone, 0, 1) === '0') {
            $phone = '255' . substr($phone, 1);
        }
        
        // If doesn't start with 255, add it
        if (substr($phone, 0, 3) !== '255') {
            $phone = '255' . $phone;
        }
        
        return $phone;
    }

    /**
     * Map PesaPal status codes to our status
     */
    protected function mapPesaPalStatus($statusCode)
    {
        $statusMap = [
            '0' => 'pending',
            '1' => 'completed',
            '2' => 'failed',
            '3' => 'pending', // Revoked
            '4' => 'failed',   // Failed
            '5' => 'cancelled' // Cancelled
        ];

        return $statusMap[$statusCode] ?? 'pending';
    }

    /**
     * Retry payment for failed transaction
     */
    public function retryPayment($id)
    {
        $payment = Payment::findOrFail($id);
        
        if ($payment->status !== 'failed' && $payment->status !== 'cancelled') {
            return redirect()->route('payment.status', ['reference' => $payment->transaction_reference])
                ->with('info', 'This payment is still processing')
                ->header('ngrok-skip-browser-warning', 'true');
        }

        return response()
            ->view('payments.retry', compact('payment'))
            ->header('ngrok-skip-browser-warning', 'true');
    }
}