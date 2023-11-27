<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoice_id', 'business_id', 'client_id', 
        'status_id', 'amount', 'balance', 'description'
    ];

    protected $casts = [
        'description' => 'array',
        'amount' => 'float',
        'balance' => 'float'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    
}
