<?php

namespace App\Services\Payments;

use App\Models\Client;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Gateway;
use App\Models\TransactionProduct;
use App\Enums\TransactionStatus;
use App\Services\Gateways\GatewayOneService;
use App\Services\Gateways\GatewayTwoService;

class PaymentService
{
    public function process(array $data)
    {
       
        $client = Client::firstOrCreate([
            'email' => $data['client']['email']
        ], [
            'name' => $data['client']['name']
        ]);

      
        $products = collect($data['products'])->map(function ($item) {
            $product = Product::findOrFail($item['product_id']);

            return [
                'product' => $product,
                'quantity' => $item['quantity']
            ];
        });

      
        $amount = $products->sum(function ($item) {
            return $item['product']->amount * $item['quantity'];
        });

    
        $transaction = Transaction::create([
            'client_id' => $client->id,
            'status' => TransactionStatus::FAILED,
            'amount' => $amount,
            'card_last_numbers' => substr($data['card_number'], -4)
        ]);

       
        foreach ($products as $item) {
            TransactionProduct::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item['product']->id,
                'quantity' => $item['quantity']
            ]);
        }

      
        $gateways = Gateway::where('is_active', true)
            ->orderBy('priority')
            ->get();

        foreach ($gateways as $gateway) {

            try {

                $service = match($gateway->name) {
                    'gateway_one' => new GatewayOneService(),
                    'gateway_two' => new GatewayTwoService(),
                };

                $response = $service->charge([
                    'amount' => $amount,
                    'name' => $client->name,
                    'email' => $client->email,
                    'card_number' => $data['card_number'],
                    'cvv' => $data['cvv']
                ]);

                if ($response) {

                    $transaction->update([
                        'gateway_id' => $gateway->id,
                        'external_id' => $response['id'] ?? null,
                        'status' => TransactionStatus::SUCCESS
                    ]);

                    return $transaction;
                }

            } catch (\Exception $e) {
                continue;
            }
        }

        throw new \Exception("All gateways failed.");
    }
}