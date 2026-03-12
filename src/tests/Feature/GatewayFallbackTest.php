<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Gateway;
use App\Models\Transaction;
use App\Services\Payments\PaymentService;
use App\Enums\TransactionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;


class GatewayFallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_fallback_to_second_gateway_when_first_fails()
    {
         Http::fake([
            '*' => Http::response([
                'id' => 'fake_transaction'
            ], 200)
        ]); 
        $product = Product::factory()->create([
            'amount' => 1000
        ]);

        Gateway::create([
            'name' => 'gateway_one',
            'priority' => 1,
            'is_active' => true
        ]);

        Gateway::create([
            'name' => 'gateway_two',
            'priority' => 2,
            'is_active' => true
        ]);

        $transaction = Transaction::factory()->create([
            'status' => 'pending'
        ]);

        $payload = [
            'client' => [
                'name' => 'Carlos',
                'email' => 'carlos@email.com'
            ],
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1
                ]
            ],
            'card_number' => '5569000000006063',
            'cvv' => '010'
        ];

        $service = app(PaymentService::class);

        $service->process($transaction, $payload);

        $this->assertEquals(TransactionStatus::SUCCESS, $transaction->fresh()->status);
    }
}