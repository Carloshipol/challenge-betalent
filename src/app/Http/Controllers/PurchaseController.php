<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Client;
use App\Models\Transaction;
use App\Enums\TransactionStatus;
use App\Jobs\ProcessPayment;

class PurchaseController extends Controller
{
    public function store(PurchaseRequest $request)
    {
        $data = $request->validated();

        $client = Client::firstOrCreate(
            ['email' => $data['client']['email']],
            ['name' => $data['client']['name']]
        );

        $transaction = Transaction::create([
            'client_id' => $client->id,
            'status' => TransactionStatus::PENDING,
            'amount' => 0,
            'card_last_numbers' => substr($data['card_number'], -4)
        ]);

        ProcessPayment::dispatch($transaction->id, $data);

       return response()->json([
            'message' => 'Payment is being processed',
            'transaction_id' => $transaction->id,
            'status' => $transaction->status
        ], 201);
    }
}