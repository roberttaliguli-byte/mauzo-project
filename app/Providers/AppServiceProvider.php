<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Bidhaa;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Hakikisha tuwezi kufanya queries za database
        try {
            DB::connection()->getPdo();

            // Hakikisha meza zipo
            if (!Schema::hasTable('bidhaas') || !Schema::hasTable('companies')) {
                $this->shareEmptyData();
                return;
            }

            $today = Carbon::today();

            // ALERTS: expired, expiring soon, or OUT OF STOCK
            $alertsCount = Bidhaa::whereDate('expiry', '<', $today)
                ->orWhereDate('expiry', '<=', $today->copy()->addDays(30))
                ->orWhere('idadi', '<=', 0)
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

        } catch (\Exception $e) {
            // Database haipo au haijaandaliwa bado
            $this->shareEmptyData();
        }
    }

    private function shareEmptyData(): void
    {
        View::share([
            'alertsCount' => 0,
            'messagesCount' => 0,
            'alertsList' => collect(),
        ]);
    }
}

