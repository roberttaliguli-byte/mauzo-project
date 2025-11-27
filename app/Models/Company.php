<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
    'company_name', 'owner_name', 'owner_gender', 'owner_dob',
    'location', 'region', 'phone', 'email',
    'is_verified', 'package', 'database_name'
];


    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function user()
{
    return $this->hasOne(\App\Models\User::class, 'company_id');
}


    // Relationships to data
public function bidhaa()
{
    return $this->hasMany(\App\Models\Bidhaa::class);
}

    public function wateja()
    {
        return $this->hasMany(Mteja::class);
    }


public function wafanyakazi()
{
    return $this->hasMany(\App\Models\Wafanyakazi::class);
}

    public function masaplaya()
{
    return $this->hasMany(\App\Models\Masaplaya::class);
}

    public function matumizi()
{
    return $this->hasMany(\App\Models\Matumizi::class);
}

    public function manunuzi()
    {
        return $this->hasMany(\App\Models\Manunuzi::class);
    }

    public function mauzo()
    {
        return $this->hasMany(\App\Models\Mauzo::class);
    }

    public function madeni()
    {
        return $this->hasMany(\App\Models\Madeni::class);
    }
    public function marejeshos()
    {
        return $this->hasMany(\App\Models\Marejesho::class);
    }
    
}