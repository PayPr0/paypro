<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'open', 'tag' => 'warning', 'color' => 'orange'],
            ['name' => 'close', 'tag' => 'danger', 'color' => 'red'],
            ['name' => 'pending', 'tag' => 'info', 'color' => 'blue'],
            ['name' => 'nopaid', 'tag' => 'warning', 'color' => 'yellow'],
            ['name' => 'active', 'tag' => 'success', 'color' => 'green'],
            ['name' => 'inactive', 'tag' => 'secondary', 'color' => 'grey'],
        ];

        foreach ($statuses as $status) {
            Status::create($status);
        }
    }
}
