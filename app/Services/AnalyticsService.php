<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function dailyTransaction($businessId)
    {
        $dailyInvoices = Invoice::where('status_id', statusId('paid'))
            ->where('created_at', '>=', now()->subMonth())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as sum'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();
      
        return $dailyInvoices;
    }

    public function paymentByMethod($businessId)
    {
        $paymentByMethod = Invoice::join('payments','payments.invoice_id','=','invoices.id')
            ->where('status_id', statusId('paid'))
            ->select('payments.payment_method as method', DB::raw('SUM(payments.amount) as sum'))
            ->groupBy('method')
            ->get()
            ->toArray();

        // Generate and assign color to each payment sum
        foreach ($paymentByMethod as &$payment) {
            $payment['color'] = $this->colorPalette();
        }

        return $paymentByMethod;
    }

    public function totalPendingBalance($businessId)
    {
        $amount = Invoice::where('status_id', statusId('pending'))
                    ->where('business_id', $businessId)
                    ->sum('balance');
        
        return ['total pending sum' => $amount];
    }

    public function noOfClients($businessId)
    {
        $business = Business::find($businessId);

        if($business){
            return ['numClient' => $business->clients()->count()];
        }

        return ['numClient' => 0];
    }

    private function colorPalette()
    {
        $hue = rand(0, 360);
        return "hsl({$hue}, 100%, 80%)";
    }
}