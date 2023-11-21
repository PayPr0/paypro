<?php

namespace Database\Seeders;

use App\Models\Business;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        for ($i = 1; $i <= 3; $i++) {
            Business::create([
                'name' => 'Business ' . $i,
                'email' => 'business' . $i . '@example.com',
                'phone' => '123456789' . $i,
                'address' => $i . '23 Street, City, Country',
                'password' => Hash::make('password'),
                'acct_number' => '000000000' . $i ,
                'bank_name' => 'Bank ' . $i,
                'business_type_id' => $i
            ]);
        }
    }
}
