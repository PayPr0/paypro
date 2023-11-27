<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Transaction;
use App\Models\Wallet;

class WalletService
{
    public function walletBalance(Business $business)
    {

        $walletZero = Wallet::where('business_id',$business->id)->first();
       
        $transactionCrSum = Transaction::where('transaction_type', 'cr')
            ->where('id', '>', $walletZero->transaction_id ?? 0)
            ->sum('amount');

        $transactionDrSum = Transaction::where('transaction_type', 'dr')
        ->where('id', '>', $walletZero->transaction_id ?? 0)
        ->sum('amount');

        $balance = $transactionCrSum - $transactionDrSum; 

        if($balance == 0){
            $lastTran = Transaction::where('business_id',$business->id)
                                    ->latest('id')
                                    ->first();
            
            $walletZero->update(['transaction_id'=>$lastTran->id ?? null]);
        }

        return $balance;
        
    }

    public function withdrawWalletFont(Business $business,float $amount):bool
    {
       if ($this->walletBalance($business) - $amount < 0){
         return false;
       }

        Transaction::create([
            'transaction_type' => 'dr',
            'amount' => $amount,
            'business_id' => $business->id
        ]);

        return true;

    }

    public function createTransaction(?string $type = 'cr',float $amount, $business_id)
    {
       return Transaction::create([
            'transaction_type' => $type ?? 'cr',
            'amount' => $amount,
            'business_id' => $business_id
        ]);
    }
}