<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['transaction_type', 'amount', 'business_id'];

    protected $casts = [
        'amount' => 'float',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
