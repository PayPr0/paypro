<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;

class BusinessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "name"=> $this->name,
            "email"=> $this->email,
            "phone"=> $this->phone,
            "address"=> $this->address,
            "acct_number"=> $this->acct_number,
            "bank_name"=> $this->bank_name,
            "business_type"=> $this->businessType->name,
            "created_at"=>Date("d-M-Y H:i:s", strtotime($this->created_at)),
        ];
    }
}
