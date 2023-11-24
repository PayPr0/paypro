<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            'invoice_no' => $this->invoice_id,
            'business_id' => $this->business_id,
            'client_id' => $this->client_id,
            'amount' => $this->amount,
            'description' => $this->description,
            'balance' => $this->balance,
            'status' => StatusResource::make($this->status),
            'client' => ClientResource::make($this->client),
            'business' => BusinessResource::make($this->business)
        ];
    }
}
