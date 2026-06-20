<?php
// app/Models/Mteja.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mteja extends Model
{
    use HasFactory;

    protected $fillable = [
        'jina',
        'simu',
        'barua_pepe',
        'anapoishi',
        'maelezo',
        'company_id',
        'customer_code',
        'registered_from', // 'boss' or 'showcase'
    ];

    /**
     * Generate customer code from phone number (for showcase registration)
     */
    public static function generateCustomerCodeFromPhone($phone, $companyId)
    {
        // Clean phone number - remove non-digits
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        // Take last 6 digits of phone number
        $phoneCode = substr($cleanPhone, -6);
        
        // Add prefix and company identifier
        $prefix = 'CUST';
        $companyCode = str_pad($companyId, 3, '0', STR_PAD_LEFT);
        
        // Check if code already exists
        $baseCode = $prefix . $companyCode . $phoneCode;
        $existing = self::where('company_id', $companyId)
            ->where('customer_code', $baseCode)
            ->first();
        
        if ($existing) {
            // If exists, add random suffix
            $suffix = str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT);
            return $prefix . $companyCode . $phoneCode . $suffix;
        }
        
        return $baseCode;
    }

    /**
     * Generate customer code (for boss registration)
     */
    public static function generateCustomerCode($companyId)
    {
        $prefix = 'CUST-';
        $year = date('Y');
        $month = date('m');
        
        $lastCustomer = self::where('company_id', $companyId)
            ->where('customer_code', 'LIKE', $prefix . $year . $month . '%')
            ->orderBy('customer_code', 'desc')
            ->first();
        
        if ($lastCustomer && $lastCustomer->customer_code) {
            $lastNumber = intval(substr($lastCustomer->customer_code, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $year . $month . '-' . $newNumber;
    }

    /**
     * Find or create customer by phone (for showcase)
     */
    public static function findOrCreateFromShowcase($phone, $companyId, $data = [])
    {
        // Clean phone number
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        // Try to find existing customer
        $customer = self::where('company_id', $companyId)
            ->where('simu', $cleanPhone)
            ->first();
        
        if ($customer) {
            // Update existing customer info if needed
            if (!empty($data['jina'])) {
                $customer->jina = $data['jina'];
            }
            if (!empty($data['barua_pepe'])) {
                $customer->barua_pepe = $data['barua_pepe'];
            }
            if (!empty($data['anapoishi'])) {
                $customer->anapoishi = $data['anapoishi'];
            }
            $customer->save();
            
            return $customer;
        }
        
        // Create new customer
        $customerCode = self::generateCustomerCodeFromPhone($cleanPhone, $companyId);
        
        return self::create([
            'company_id' => $companyId,
            'customer_code' => $customerCode,
            'jina' => $data['jina'] ?? 'Mteja wa Showcase',
            'simu' => $cleanPhone,
            'barua_pepe' => $data['barua_pepe'] ?? null,
            'anapoishi' => $data['anapoishi'] ?? null,
            'maelezo' => 'Registered via showcase order',
            'registered_from' => 'showcase',
        ]);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}