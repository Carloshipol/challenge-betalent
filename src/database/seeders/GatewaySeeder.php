<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gateway;

class GatewaySeeder extends Seeder
{
    public function run()
    {
        Gateway::updateOrCreate(
            ['name' => 'gateway_one'],
            ['is_active' => true, 'priority' => 1]
        );

        Gateway::updateOrCreate(
            ['name' => 'gateway_two'],
            ['is_active' => true, 'priority' => 2]
        );
    }
}