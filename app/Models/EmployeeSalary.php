<?php
// app/Models/EmployeeSalary.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    use HasFactory;

    protected $table = 'employee_salaries';

    protected $fillable = [
        'company_id',
        'mfanyakazi_id',
        'salary_amount',
        'currency',
        'frequency',
        'effective_date',
        'remarks',
        'created_by'
    ];

    protected $casts = [
        'salary_amount' => 'float',
        'effective_date' => 'date'
    ];

    public function employee()
    {
        return $this->belongsTo(Wafanyakazi::class, 'mfanyakazi_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}