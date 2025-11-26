<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Bidhaa;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

 public function boot(): void
{
    $today = Carbon::today();

    // ALERTS: expired, expiring soon, or OUT OF STOCK
    $alertsCount = Bidhaa::whereDate('expiry', '<', $today)
        ->orWhereDate('expiry', '<=', $today->copy()->addDays(30))
        ->orWhere('idadi', '<=', 0) // out of stock
        ->count();

    // MESSAGES: companies not verified
    $messagesCount = Company::where('is_verified', 0)->count();

    // ALERT LIST: include out-of-stock too
    $alertsList = Bidhaa::whereDate('expiry', '<', $today)
        ->orWhereDate('expiry', '<=', $today->copy()->addDays(30))
        ->orWhere('idadi', '<=', 0)
        ->orderBy('expiry', 'asc')
        ->get();

    View::share([
        'alertsCount' => $alertsCount,
        'messagesCount' => $messagesCount,
        'alertsList' => $alertsList,
    ]);
}

}
