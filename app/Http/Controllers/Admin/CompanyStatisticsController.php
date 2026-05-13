<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyStatisticsController extends Controller
{
    /**
     * Display company statistics page
     */
    public function index()
    {
        // Define source labels
        $sourceLabels = [
            'friend' => 'Rafiki',
            'social_media' => 'Social Media',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'tiktok' => 'TikTok',
            'youtube' => 'YouTube',
            'google' => 'Google Search',
            'whatsapp' => 'WhatsApp',
            'old_system' => 'Nilitumia Mfumo Mwingine',
            'invited' => 'Nimealikwa',
            'advertisement' => 'Tangazo',
            'website' => 'Website',
            'customer_referral' => 'Mteja Aliyenielekeza',
            'event' => 'Event / Maonesho',
            'other' => 'Nyingine'
        ];

        // Define business type labels
        $businessLabels = [
            'retail_shop' => 'Retail Shop / Duka',
            'mini_market' => 'Mini Market',
            'supermarket' => 'Supermarket',
            'pharmacy' => 'Pharmacy / Dawa',
            'hardware' => 'Hardware',
            'stationery' => 'Stationery',
            'restaurant' => 'Restaurant',
            'hotel' => 'Hotel',
            'bar' => 'Bar / Vinywaji',
            'clothes_shop' => 'Duka la Nguo',
            'shoes_shop' => 'Duka la Viatu',
            'furniture' => 'Furniture',
            'cosmetics' => 'Cosmetics',
            'electronics' => 'Electronics',
            'salon' => 'Salon / Kinyozi',
            'spare_parts' => 'Spare Parts',
            'wholesale' => 'Jumla / Wholesale',
            'bakery' => 'Bakery',
            'grocery' => 'Grocery',
            'other' => 'Nyingine'
        ];

        // Get business type statistics
        $businessTypes = Company::select('business_type')
            ->selectRaw('COUNT(*) as total')
            ->whereNotNull('business_type')
            ->groupBy('business_type')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function ($item) use ($businessLabels) {
                $item->business_type_label = $businessLabels[$item->business_type] ?? $item->business_type ?? 'Haijabainika';
                return $item;
            });

        // Get hear about us statistics
        $hearAboutUs = Company::select('hear_about_us')
            ->selectRaw('COUNT(*) as total')
            ->whereNotNull('hear_about_us')
            ->groupBy('hear_about_us')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function ($item) use ($sourceLabels) {
                $item->hear_about_us_label = $sourceLabels[$item->hear_about_us] ?? $item->hear_about_us ?? 'Haijabainika';
                return $item;
            });

        // Get total companies count
        $totalCompanies = Company::count();
        
        // Get companies registered this month
        $thisMonthCompanies = Company::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Get companies registered last month
        $lastMonthCompanies = Company::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        // Calculate growth percentage
        $growthPercentage = $lastMonthCompanies > 0 
            ? round((($thisMonthCompanies - $lastMonthCompanies) / $lastMonthCompanies) * 100, 1)
            : ($thisMonthCompanies > 0 ? 100 : 0);

        // Get all companies for the table
        $companies = Company::latest()->take(100)->get();

        return view('admin.company-statistics', compact(
            'businessTypes', 
            'hearAboutUs', 
            'totalCompanies',
            'thisMonthCompanies',
            'lastMonthCompanies',
            'growthPercentage',
            'companies',
            'sourceLabels',
            'businessLabels'
        ));
    }
}