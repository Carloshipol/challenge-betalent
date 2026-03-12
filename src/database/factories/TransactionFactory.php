<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Transaction;
use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'status' => TransactionStatus::PENDING,
            'amount' => 1000,
            'card_last_numbers' => '1234'
        ];
    }
}