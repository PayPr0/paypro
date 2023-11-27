<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessType;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class BusinessService
{
    public function createBusiness($data)
    {
        $businessType = BusinessType::where('name',$data->business_type)->first();
        
        $business =  Business::create([
        'name' => $data->name,
        'email' => $data->email,
        'phone' => $data->phone, 
        'address' => $data->address ?? null,
        'password' => Hash::make($data->password), 
        'acct_number' => $data->acct_number, 
        'bank_name' => $data->bank_name,
        'business_type_id' => $businessType->id,
    
        ]);

        $this->createWallet($business);
        $this->createPaystackSubAccount($business);

        return $business;
        
    }

    protected function createWallet($business)
    {
        Wallet::create([
            'business_id' => $business->id,
        ]);
    }


    protected function createPaystackSubAccount($business)
    {
        $url = "https://api.paystack.co/subaccount";

       $bankCode = $this->getBankCode($business->bank_name);

        $fields = [
            'business_name' => $business->name,
            'bank_code' => $bankCode, //bank code
            'account_number' => $business->acct_number,
            'percentage_charge' => 0.8
        ];

        $response = Http::withHeaders([
                "Authorization" => env('PAYSTACK_SECRET_KEY'),
                "Cache-Control" => "no-cache",
            ])
            ->post($url, $fields);

        $result = $response->body();

        return $result;

    }


    protected function getBankCode($bankName)
    {
        $response =  Http::get('https://nigerianbanks.xyz'); //api to get all banks in nigeria and thier codes

        if ($response->successful()) {

            $banks = $response->json(); 

            $matchedBanks = array_filter($banks, function ($bank) use ($bankName) {
                return stripos($bank['name'], $bankName) !== false;
            });

            if (!empty($matchedBanks)) {
                $firstMatchedBank = reset($matchedBanks);

                $bankCode = $firstMatchedBank['code'];
                
           
                return $bankCode; 
            } else {
                return  response()->errorResonse('No bank found matching the bank inputed.');
            }
        } else {
            
            return false;
        }
    }
}
