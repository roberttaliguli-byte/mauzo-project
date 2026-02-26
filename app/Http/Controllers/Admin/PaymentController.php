<?php
// app/Http/Controllers/Admin/PaymentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Company;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Show all payments
     */
    public function index(Request $request)
    {
        $query = Payment::with('company');

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->from_date));
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->to_date));
        }

        // Search by company name or reference
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_reference', 'like', "%{$search}%")
                  ->orWhere('merchant_reference', 'like', "%{$search}%")
                  ->orWhereHas('company', function($cq) use ($search) {
                      $cq->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->latest()->paginate(15);

        $stats = [
            'total' => Payment::count(),
            'completed' => Payment::where('status', 'completed')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
            'total_amount' => Payment::where('status', 'completed')->sum('amount'),
            'today_amount' => Payment::where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('amount')
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Show payment details
     */
    public function show($id)
    {
        $payment = Payment::with('company')->findOrFail($id);
        
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Verify payment manually
     */
    public function verifyPayment(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject'
        ]);

        $payment = Payment::findOrFail($id);

        if ($payment->status === 'completed') {
            return back()->with('error', 'Payment already completed');
        }

        if ($request->action === 'approve') {
            $payment->update(['status' => 'completed']);
            
            // Activate package for company
            $this->activatePackageManually($payment);
            
            $message = 'Payment approved and package activated successfully';
        } else {
            $payment->update(['status' => 'failed']);
            $message = 'Payment rejected';
        }

        return back()->with('success', $message);
    }

    /**
     * Manually activate package
     */
    protected function activatePackageManually($payment)
    {
        $company = $payment->company;
        $start_date = Carbon::now();
        
        switch($payment->package_type) {
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
    }

    /**
     * Export payments report
     */
    public function export(Request $request)
    {
        $query = Payment::with('company');

        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->from_date));
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->to_date));
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $payments = $query->get();

        // Generate CSV
        $filename = 'payments_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Add headers
        fputcsv($handle, [
            'ID', 'Company', 'Transaction Ref', 'Merchant Ref', 'Package',
            'Amount', 'Currency', 'Phone', 'Payment Method', 'Status',
            'Payment Date', 'Expiry Date', 'Created At'
        ]);

        // Add data
        foreach ($payments as $payment) {
            fputcsv($handle, [
                $payment->id,
                $payment->company->company_name ?? 'N/A',
                $payment->transaction_reference,
                $payment->merchant_reference,
                $payment->package_type,
                $payment->amount,
                $payment->currency,
                $payment->phone_number,
                $payment->payment_method,
                $payment->status,
                $payment->payment_date ? $payment->payment_date->format('Y-m-d H:i') : 'N/A',
                $payment->expiry_date ? $payment->expiry_date->format('Y-m-d') : 'N/A',
                $payment->created_at->format('Y-m-d H:i')
            ]);
        }

        fclose($handle);
        exit;
    }
}
