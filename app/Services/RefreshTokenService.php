<?php

namespace App\Services;

use App\Models\RefreshToken;
use Illuminate\Support\Str;

class RefreshTokenService
{

    public function createRefreshToken(array $data)
    {
        $salt = ($data['business_id'] ?? null) ? 'B' : 'C';

        return RefreshToken::updateOrCreate([
            'business_id'=>$data['business_id'] ?? null,
            'client_id'=>$data['client_id'] ?? null,            
        ],[
            'expire_at' => now()->addDay(),
            'refresh_token' => Str::random().$salt.time()
        ]);   
    }

}