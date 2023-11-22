<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Client extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = "clients";

    protected $fillable = ['name', 'email', 'phone','address'];

    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_clients');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeBusnessClient(Builder $query):void
    {
        $query->wherePivot('business_id',auth()->id());
    }
}
    
