<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientOtpToken extends Model
{
    use HasFactory;

    protected $fillable = ['expire_at', 'token', 'client_id'];
    
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

}
