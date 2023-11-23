<?php

namespace App\Models;

use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Business extends Model
{
    use HasFactory, HasApiTokens ;

    protected $table = "businesses";

    protected $fillable = [
        'name', 'email', 'phone', 'address', 
        'password', 'acct_number', 'bank_name', 
        'business_type_id'
    ];

    protected $hidden = [
        'password',
    ];
    
    public function businessType()
    {
        return $this->belongsTo(BusinessType::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'business_clents');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }


}
