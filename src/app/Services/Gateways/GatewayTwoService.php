<?php

namespace App\Services\Gateways;

use Illuminate\Support\Facades\Http;

class GatewayTwoService implements GatewayInterface
{
    private string $baseUrl = 'http://localhost:3002';

    public function charge(array $data): array
    {
        $response = Http::withHeaders([
            'Gateway-Auth-Token' => 'tk_f2198cc671b5289fa856',
            'Gateway-Auth-Secret' => '3d15e8ed6131446ea7e3456728b1211f'
        ])->post($this->baseUrl . '/transacoes', [
            'valor' => $data['amount'],
            'nome' => $data['name'],
            'email' => $data['email'],
            'numeroCartao' => $data['card_number'],
            'cvv' => $data['cvv']
        ]);

        return $response->json();
    }

    public function refund(string $transactionId): bool
    {
        $response = Http::withHeaders([
            'Gateway-Auth-Token' => 'tk_f2198cc671b5289fa856',
            'Gateway-Auth-Secret' => '3d15e8ed6131446ea7e3456728b1211f'
        ])->post($this->baseUrl . '/transacoes/reembolso', [
            'id' => $transactionId
        ]);

        return $response->successful();
    }
}