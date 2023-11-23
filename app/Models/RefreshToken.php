<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'client_id',
        'refresh_token',
        'expire_at',
    ];

    public function hasExpired()
    {
        return now()->gte($this->updated_at->addSeconds($this->expire_at));
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
