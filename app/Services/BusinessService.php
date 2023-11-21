<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessType;
use Illuminate\Support\Facades\Hash;

class BusinessService
{
    public function createBusiness($data)
    {
        $businessType = BusinessType::where('name',$data->business_type)->first();
        
        return Business::create([
        'name' => $data->name,
        'email' => $data->email,
        'phone' => $data->phone, 
        'address' => $data->address ?? null,
        'password' => Hash::make($data->password), 
        'acct_number' => $data->acct_number, 
        'bank_name' => $data->bank_name,
        'business_type_id' => $businessType->id,
    
    ]);
    }
}
