<?php

namespace App\Services;

use App\Models\Invoice;

class InvoiceService 
{
    public function createInvoiceForClient(string $invoice_id , $business_id, $client_id, float $amount, string $description, float $balance = null)
    {
      
        return Invoice::create([
            'invoice_id' => $invoice_id,
            'business_id' => $business_id,
            'client_id' => $client_id,
            'amount' => $amount,
            'description' => $description,
            'balance' => $balance,
            'status_id' => statusId(config('status.Pending'))
        ]);
    }

    public function updateInvoice(string $invoice_id, array $updates)
    {
        return Invoice::where('invoice_id', $invoice_id)->update($updates);
    }
    
}
