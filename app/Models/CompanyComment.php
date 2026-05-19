<?php
// app/Models/CompanyComment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyComment extends Model
{
    protected $fillable = ['company_id', 'user_id', 'comment'];
    
    protected $table = 'company_comments';
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}