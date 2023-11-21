<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $businessTypes = [
            'tech', 
            'agriculture', 
            'fashion',
            'trader',
            'distributor', 
            'wholeseller', 
            'business person'
        ];

        foreach ($businessTypes as $type) {
            BusinessType::create(['name' => $type]);
        }
    }
}
