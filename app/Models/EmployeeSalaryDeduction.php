<?php
// app/Models/EmployeeSalaryDeduction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalaryDeduction extends Model
{
    use HasFactory;

    protected $table = 'employee_salary_deductions';

    protected $fillable = [
        'company_id',
        'mfanyakazi_id',
        'order_id',
        'amount',
        'reason',
        'status',
        'remarks',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'amount' => 'float',
        'approved_at' => 'datetime'
    ];

    public function employee()
    {
        return $this->belongsTo(Wafanyakazi::class, 'mfanyakazi_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}