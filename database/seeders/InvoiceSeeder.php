<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $business = Business::find(1);


        DB::beginTransaction();

        $client = Client::create(
            [
                'email' => 'fake@mail.com',
                'phone' => fake()->phoneNumber(),
                'name' => fake()->name(),
                'address' => fake()->address(),
            ]
        );

        $client->businesses()->attach($business, [
            'client_business_id' => Str::slug($business->name . "/" . $client->id . time())
        ]);

        DB::commit();

        Invoice::create([
            'invoice_id' => uniqid(Str::substr($business->name, 0, 3)),
            'business_id' => $business->id,
            'client_id' => $client->id,
            'status_id' => statusId(config('status.Pending')),
            'amount' => 10000,
            'balance' => 10000,
            'description' => [
                'gas' => 3000,
                '2 catton of noodles' => 7000
            ]
        ]);
    }
}
