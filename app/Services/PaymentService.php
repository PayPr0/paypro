<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;

class PaymentService
{

    public function createPayment(array $data)
    {
        // Create payment using the payment data
        $payment = Payment::create([
            'invoice_id' => $data['invoice_id'],
            'business_id' => $data['business_id'],
            'is_online' => $data['payment_method'] !== 'cash',
            'payment_method' => $data['payment_method'],
            'amount' => $data['amount'],
            'client_id' => $data['client_id'],
        ]);

        $this->regTransaction($payment);

        return $payment;

    }


    /**
     * Verify a transaction with Paystack API
     *
     * @param string $transactionReference
     * @return array
     */
    public function verifyTransaction($transactionReference)
    {
        $url = 'https://api.paystack.co/transaction/verify/' . $transactionReference;
          // Make an API call to verify the transaction
     
        $responseData = Http::withHeaders([
            'Authorization' => 'Bearer ' . config("paystack.secret")
        ])->get($url);

        // Check if the status is successful
        if ($responseData['status'] != true) {
            return false;
        }
        
        return [
            'amount' => $responseData['data']['amount'] / 100,
            'channel' => $responseData['data']['channel'],
            'email' => $responseData['data']['customer']['email'],
        ];
    }   

    protected function regTransaction($payment)
    {

       $wallet = app(WalletService::class);

        $wallet->createTransaction('cr', $payment->amount, $payment->business_id);
        $business = Business::find($payment->business_id);

        if($business){
            $wallet->walletBalance( $business);
        }
        
    }
}