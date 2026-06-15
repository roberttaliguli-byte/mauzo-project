<?php
namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityHelper
{
    public static function log($activityType, $description, $model = null, $amount = null)
    {
        $user = null;
        $userName = 'System';
        $userRole = 'System';
        $userId = null;
        $mfanyakaziId = null;
        $companyId = null;
        
        // Check for boss (User)
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $userName = $user->name ?? $user->username;
            $userRole = 'Boss';
            $userId = $user->id;
            $companyId = $user->company_id;
        } 
        // Check for employee (Wafanyakazi)
        elseif (Auth::guard('mfanyakazi')->check()) {
            $user = Auth::guard('mfanyakazi')->user();
            $userName = $user->jina;
            $userRole = $user->role ?? 'Employee';
            $mfanyakaziId = $user->id;
            $companyId = $user->company_id;
        }
        
        if (!$companyId) {
            return null;
        }
        
        $logData = [
            'company_id' => $companyId,
            'user_id' => $userId,
            'mfanyakazi_id' => $mfanyakaziId,
            'user_name' => $userName,
            'user_role' => $userRole,
            'activity_type' => $activityType,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'amount' => $amount
        ];
        
        if ($model) {
            $logData['model_type'] = get_class($model);
            $logData['model_id'] = $model->id;
        }
        
        return ActivityLog::create($logData);
    }
    
    // Specific log methods
    public static function logSale($sale, $bidhaaJina, $jumla)
    {
        return self::log('sale', "Mauzo: {$bidhaaJina} - Tsh " . number_format($jumla, 0), $sale, $jumla);
    }
    
    public static function logPurchase($purchase, $bidhaaJina, $bei)
    {
        return self::log('purchase', "Manunuzi: {$bidhaaJina} - Tsh " . number_format($bei, 0), $purchase, $bei);
    }
    
    public static function logExpense($expense, $maelezo, $gharama)
    {
        return self::log('expense', "Matumizi: {$maelezo} - Tsh " . number_format($gharama, 0), $expense, $gharama);
    }
    
    public static function logRepayment($repayment, $bidhaaJina, $kiasi)
    {
        return self::log('repayment', "Marejesho ya Deni: {$bidhaaJina} - Tsh " . number_format($kiasi, 0), $repayment, $kiasi);
    }
    
    public static function logLogin($user, $type)
    {
        $name = $type === 'boss' ? ($user->name ?? $user->username) : $user->jina;
        return self::log('login', "{$name} alichungulia mfumo", null, null);
    }
    
    public static function logLogout($user, $type)
    {
        $name = $type === 'boss' ? ($user->name ?? $user->username) : $user->jina;
        return self::log('logout', "{$name} alitoka mfumo", null, null);
    }
}