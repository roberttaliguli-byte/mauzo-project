<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
  public function index()
{
    $company = Auth::user()->company;

    if (!$company) {
        return redirect()->back()->with('error', 'User has no company assigned!');
    }

    $totalBidhaa      = $company->bidhaa()->count();
    $totalWafanyakazi = $company->wafanyakazi()->count();
    $totalMasaplaya   = $company->masaplaya()->count();
    $totalWateja      = $company->wateja()->count();

    return view('dashboard', compact(
        'totalBidhaa',
        'totalWafanyakazi',
        'totalMasaplaya',
        'totalWateja'
    ));
}

}
