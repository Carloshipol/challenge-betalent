<?php

namespace App\Services\Payments;

use App\Models\Gateway;
use App\Services\Gateways\GatewayOneService;
use App\Services\Gateways\GatewayTwoService;

class PaymentService
{
    public function process(array $data)
    {
        $gateways = Gateway::where('is_active', true)
            ->orderBy('priority')
            ->get();

        foreach ($gateways as $gateway) {

            try {

                $service = match($gateway->name) {
                    'gateway_one' => new GatewayOneService(),
                    'gateway_two' => new GatewayTwoService(),
                };

                $response = $service->charge($data);

                if (!empty($response)) {
                    return $response;
                }

            } catch (\Exception $e) {
                continue;
            }
        }

        throw new \Exception("All gateways failed");
    }
}