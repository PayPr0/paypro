<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

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
        return $this->belongsToMany(Client::class, 'business_clients');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
