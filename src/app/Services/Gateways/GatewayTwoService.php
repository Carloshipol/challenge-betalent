<?php

namespace App\Services\Gateways;

use Illuminate\Support\Facades\Http;
use Exception;

class GatewayTwoService implements GatewayInterface
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.gateway_two.url');
    }

    public function charge(array $data): array
    {
        $response = Http::timeout(5)
            ->withHeaders([
                'Gateway-Auth-Token' => 'tk_f2198cc671b5289fa856',
                'Gateway-Auth-Secret' => '3d15e8ed6131446ea7e3456728b1211f'
            ])
            ->post($this->baseUrl . '/transacoes', [
                'valor' => $data['amount'],
                'nome' => $data['name'],
                'email' => $data['email'],
                'numeroCartao' => $data['card_number'],
                'cvv' => $data['cvv']
            ]);

        $body = $response->json();

        if (!$response->successful() || isset($body['error'])) {
            throw new Exception('GatewayTwo charge failed');
        }

        return $body;
    }

    public function refund(string $transactionId): bool
    {
        $response = Http::timeout(5)
            ->withHeaders([
                'Gateway-Auth-Token' => 'tk_f2198cc671b5289fa856',
                'Gateway-Auth-Secret' => '3d15e8ed6131446ea7e3456728b1211f'
            ])
            ->post($this->baseUrl . '/transacoes/reembolso', [
                'id' => $transactionId
            ]);

        if (!$response->successful()) {
            throw new Exception('GatewayTwo refund failed');
        }

        return true;
    }
}