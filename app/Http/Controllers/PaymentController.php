<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Pesapal\PesapalService;

class PaymentController extends Controller
{
    protected $pesapal;

    public function __construct()
    {
        $this->pesapal = new PesapalService();
    }

    public function pay()
    {
        $redirectUrl = $this->pesapal->initiatePayment([
            'amount' => 1000,
            'currency' => 'USD',
            'description' => 'Test Order rgba(44, 171, 93, 1)',
            'reference' => 'ORDER123',
            'first_name' => 'robert',
            'last_name' => 'taliguli',
            'email' => 'roberttaliguli@gmail.com',
            'phone' => '0614356830'
        ]);

        return redirect($redirectUrl);
    }

    public function callback(Request $request)
    {
        $trackingId = $request->get('pesapal_tracking_id');
        $merchantRef = $request->get('pesapal_merchant_reference');

        $status = $this->pesapal->checkPaymentStatus($trackingId, $merchantRef);

        // Handle status
        return response()->json([
            'tracking_id' => $trackingId,
            'merchant_ref' => $merchantRef,
            'status' => $status
        ]);
    }
}
