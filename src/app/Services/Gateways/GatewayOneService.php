<?php

namespace App\Services\Gateways;

use Illuminate\Support\Facades\Http;

class GatewayOneService implements GatewayInterface
{
    private string $baseUrl = 'http://localhost:3001';

    private function authenticate(): string
    {
        $response = Http::post($this->baseUrl . '/login', [
            'email' => 'dev@betalent.tech',
            'token' => 'FEC9BB078BF338F464F96B48089EB498'
        ]);

        return $response->json()['token'];
    }

    public function charge(array $data): array
    {
        $token = $this->authenticate();

        $response = Http::withToken($token)
            ->post($this->baseUrl . '/transactions', [
                'amount' => $data['amount'],
                'name' => $data['name'],
                'email' => $data['email'],
                'cardNumber' => $data['card_number'],
                'cvv' => $data['cvv']
            ]);

        return $response->json();
    }

    public function refund(string $transactionId): bool
    {
        $token = $this->authenticate();

        $response = Http::withToken($token)
            ->post($this->baseUrl . "/transactions/{$transactionId}/charge_back");

        return $response->successful();
    }
}