<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCleanupLog extends Model
{
    protected $guarded = [];
    protected $casts = ['criteria' => 'array', 'products' => 'array'];
}
