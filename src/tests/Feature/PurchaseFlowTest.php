<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Jobs\ProcessPayment;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_dispatches_payment_job()
    {
        Queue::fake();

        $product = Product::factory()->create([
            'amount' => 1000
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

        $response = $this->postJson('/api/purchase', $payload);

        $response->assertStatus(200);

        Queue::assertPushed(ProcessPayment::class);
    }
}