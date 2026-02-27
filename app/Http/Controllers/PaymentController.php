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

        $packages = [
            '30 days' => [
                'price' => 1000,
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
            
            if (!$package) {
                $package = session('selected_package');
            }
            
            if (!$package) {
                return redirect()->route('payment.package.selection')
                    ->with('error', 'Tafadhali chagua kifurushi kwanza.')
                    ->header('ngrok-skip-browser-warning', 'true');
            }
            
            if (!in_array($package, ['30 days', '180 days', '366 days'])) {
                return redirect()->route('payment.package.selection')
                    ->with('error', 'Kifurushi si sahihi.')
                    ->header('ngrok-skip-browser-warning', 'true');
            }
        } else {
            $request->validate([
                'package' => 'required|in:30 days,180 days,366 days'
            ]);
            $package = $request->package;
        }

        $user = Auth::user();
        $company = $user->company;
        $amount = Payment::getPackageAmount($package);

        session(['selected_package' => $package]);

        return view('payments.payment-form', compact('company', 'package', 'amount'));
    }

/**
 * Process payment with PesaPal
 */

public function processPayment(Request $request)
{
    $user = Auth::user();
    $company = $user->company;

    Log::info('Payment process started', [
        'user_id' => $user->id,
        'package' => $request->package,
        'phone' => $request->phone_number
    ]);

    // Validate input
    $request->validate([
        'package' => 'required|in:30 days,180 days,366 days',
        'phone_number' => 'required|string|max:15',
        'payment_method' => 'required|in:MIXBY_YAS,AIRTEL,VISA,MASTERCARD'
    ]);

    $package = $request->package;
    $amount = Payment::getPackageAmount($package);
    $phoneNumber = $this->formatPhoneNumber($request->phone_number);

    Log::info('Payment validation passed', [
        'amount' => $amount,
        'formatted_phone' => $phoneNumber
    ]);

    // Prevent duplicate payment within 2 minutes
    $existingPending = Payment::where('company_id', $company->id)
        ->where('status', 'pending')
        ->where('created_at', '>=', now()->subMinutes(2))
        ->first();

    if ($existingPending && isset($existingPending->payment_response_data['redirect_url'])) {
        return redirect()->away($existingPending->payment_response_data['redirect_url']);
    }

    DB::beginTransaction();

    try {

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

        // Get token
        $token = $this->pesapalService->getAccessToken();
        if (!$token) {
            throw new \Exception('Failed to get Pesapal access token');
        }

        // Register IPN
        $notificationId = config('pesapal.notification_id');
        if (!$notificationId) {
            $ipnResponse = $this->pesapalService->registerIPN($token);
            if (!$ipnResponse || !isset($ipnResponse['ipn_id'])) {
                throw new \Exception('Failed to register IPN');
            }
            $notificationId = $ipnResponse['ipn_id'];
        }

        // Prepare order
        $orderData = $this->pesapalService->prepareOrderData(
            $payment,
            $company,
            $user,
            $notificationId
        );

        $orderResponse = $this->pesapalService->submitOrder($token, $orderData);

        if (!$orderResponse || !isset($orderResponse['order_tracking_id']) || !isset($orderResponse['redirect_url'])) {
            throw new \Exception('Invalid response from PesaPal: ' . json_encode($orderResponse));
        }

        $payment->update([
            'pesapal_transaction_tracking_id' => $orderResponse['order_tracking_id'],
            'payment_response_data' => $orderResponse
        ]);

        DB::commit();

        Log::info('Redirecting user to PesaPal', [
            'redirect_url' => $orderResponse['redirect_url']
        ]);

        // ✅ THIS IS THE IMPORTANT PART
        return redirect()->away($orderResponse['redirect_url']);

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

        $payment = Payment::where('pesapal_transaction_tracking_id', $orderTrackingId)
            ->orWhere('merchant_reference', $orderMerchantReference)
            ->first();

        if (!$payment) {
            Log::error('Payment not found for callback', $request->all());
            return redirect()->route('payment.failed')
                ->with('error', 'Payment record not found')
                ->header('ngrok-skip-browser-warning', 'true');
        }

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

public function ipn(Request $request)
{
    Log::info('🔔 IPN received', $request->all());

    $trackingId = $request->input('OrderTrackingId');
    $merchantRef = $request->input('OrderMerchantReference');
    $notificationType = $request->input('pesapal_notification_type');

    if (!$trackingId) {
        return response('Invalid Request', 400);
    }

    $payment = Payment::where('pesapal_transaction_tracking_id', $trackingId)
        ->orWhere('merchant_reference', $merchantRef)
        ->first();

    if (!$payment) {
        return response('Payment Not Found', 404);
    }

    // 🚫 Prevent double processing
    if ($payment->status === 'completed') {
        return response("pesapal_notification_type=$notificationType&pesapal_transaction_tracking_id=$trackingId&pesapal_merchant_reference=$merchantRef");
    }

    $token = $this->pesapalService->getAccessToken();
    $statusResponse = $this->pesapalService->getTransactionStatus($token, $trackingId);

    if ($statusResponse && isset($statusResponse['status_code'])) {

        $newStatus = $this->mapPesaPalStatus($statusResponse['status_code']);

        $payment->update([
            'ipn_data' => $request->all(),
            'status' => $newStatus,
            'payment_response_data' => array_merge(
                $payment->payment_response_data ?? [],
                ['ipn_status' => $statusResponse]
            )
        ]);

        if ($newStatus === 'completed') {
            $this->activatePackage($payment);
        }
    }

    return response("pesapal_notification_type=$notificationType&pesapal_transaction_tracking_id=$trackingId&pesapal_merchant_reference=$merchantRef")
        ->header('Content-Type', 'text/plain');
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

        $company->package = $payment->package_type;
        $company->package_start = $start_date;
        $company->package_end = $end_date;
        $company->save();

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
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phone, 0, 1) === '0') {
            $phone = '255' . substr($phone, 1);
        }
        
        if (substr($phone, 0, 3) !== '255') {
            $phone = '255' . $phone;
        }
        
        // Ensure it's exactly 12 digits for Tanzania
        $phone = substr($phone, 0, 12);
        
        Log::info('Phone formatted', [
            'original' => request()->phone_number,
            'formatted' => $phone,
            'length' => strlen($phone)
        ]);
        
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
            '3' => 'pending',
            '4' => 'failed',
            '5' => 'cancelled'
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