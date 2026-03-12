<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Client;
use App\Models\Transaction;
use App\Enums\TransactionStatus;
use App\Jobs\ProcessPayment;
use App\Models\Product;

class PurchaseController extends Controller
{
    public function store(PurchaseRequest $request)
    {
        $data = $request->validated();

        $client = Client::firstOrCreate(
            ['email' => $data['client']['email']],
            ['name' => $data['client']['name']]
        );

        $amount = 0;

        foreach ($data['products'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $amount += $product->amount * $item['quantity'];
        }

        $transaction = Transaction::create([
            'client_id' => $client->id,
            'status' => TransactionStatus::PENDING,
            'amount' => $amount,
            'card_last_numbers' => substr($data['card_number'], -4)
        ]);

        ProcessPayment::dispatch($transaction, $data);

        return response()->json([
            'transaction_id' => $transaction->id,
            'status' => $transaction->status
        ]);
    }
}