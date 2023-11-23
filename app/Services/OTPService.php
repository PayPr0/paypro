<?php

namespace App\Services;

use App\Models\Client;
use App\Models\VerificationCode;

Class OTPService 
{
    public function generateOtp(Client $client)
    {
        // Client Does not have Existin OTP
        $verificationcode = VerificationCode::where('client_id',$client->id)->latest()->first();

        if($verificationcode && now()->isBefore($verificationcode->expire_at)){
            return $verificationcode;
        }

        if($verificationcode){
            $verificationcode->update([
                'otp' => $this::getToken(),
                'expire_at'=> now()->addMinutes(10),
            ]);

            return $verificationcode;
        }

        return VerificationCode::create([
            'client_id' => $client->id,
            'otp' => $this::getToken(),
            'expire_at' => now()->addMinutes(10),
        ]);
    }

    static public function getToken()
    {
        return uniqid();
    }

    public function verifyOtp($client_id,$token):array
    {
        $verificationcode = VerificationCode::where('client_id',$client_id)
                                            ->where('otp',$token)
                                            ->first();
        
        if(!$verificationcode){
            return [false,'Incorrect OTP'];
        }

        if($verificationcode && now()->isAfter($verificationcode->expire_at)){
            return [false, 'Expired OTP'];
        }

        $verificationcode->update([
            'expire_at' => now()
        ]);

        return [true,'Valid OTP'];
    }
}
