<?php

namespace App\Services\Gateways;

use Illuminate\Support\Facades\Http;
use Exception;

class GatewayOneService implements GatewayInterface
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.gateway_one.url');
    }

    private function authenticate(): string
    {
        $response = Http::timeout(5)->post($this->baseUrl . '/login', [
            'email' => 'dev@betalent.tech',
            'token' => 'FEC9BB078BF338F464F96B48089EB498'
        ]);

        if (!$response->successful()) {
            throw new Exception('GatewayOne authentication failed');
        }

        return $response->json()['token'];
    }

    public function charge(array $data): array
    {
        $token = $this->authenticate();

        $response = Http::timeout(5)
            ->withToken($token)
            ->post($this->baseUrl . '/transactions', [
                'amount' => $data['amount'],
                'name' => $data['name'],
                'email' => $data['email'],
                'cardNumber' => $data['card_number'],
                'cvv' => $data['cvv']
            ]);

        $body = $response->json();

        if (!$response->successful() || isset($body['error'])) {
            throw new Exception('GatewayOne charge failed');
        }

        return $body;
    }

    public function refund(string $transactionId): bool
    {
        $token = $this->authenticate();

        $response = Http::timeout(5)
            ->withToken($token)
            ->post($this->baseUrl . "/transactions/{$transactionId}/charge_back");

        if (!$response->successful()) {
            throw new Exception('GatewayOne refund failed');
        }

        return true;
    }
}