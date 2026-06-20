<?php
// routes/api.php

Route::get('/package-info', function () {
    $company = null;
    
    if (Auth::check()) {
        $company = Auth::user()->company;
    } elseif (Auth::guard('mfanyakazi')->check()) {
        $company = Auth::guard('mfanyakazi')->user()->company;
    }
    
    if (!$company) {
        return response()->json(['success' => false, 'message' => 'No company found']);
    }
    
    $daysLeft = 0;
    if ($company->package_end) {
        $daysLeft = max(0, ceil(now()->diffInDays(\Carbon\Carbon::parse($company->package_end), false)));
    }
    
    return response()->json([
        'success' => true,
        'days_left' => $daysLeft,
        'package_name' => $company->package ?? 'Free Trial',
        'end_date' => $company->package_end ? \Carbon\Carbon::parse($company->package_end)->format('d/m/Y') : 'N/A'
    ]);
});