<?php

namespace App\Services\Payments;

use App\Models\Transaction;
use App\Services\Gateways\GatewayOneService;
use App\Services\Gateways\GatewayTwoService;
use App\Enums\TransactionStatus;

class RefundService
{
    public function refund(int $transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);

        if (!$transaction->gateway) {
            throw new \Exception("Transaction has no gateway.");
        }

        $gateway = match ($transaction->gateway->name) {
            'gateway_one' => new GatewayOneService(),
            'gateway_two' => new GatewayTwoService(),
        };

        $success = $gateway->refund($transaction->external_id);

        if ($success) {

            $transaction->update([
                'status' => TransactionStatus::REFUNDED
            ]);

            return $transaction;
        }

        throw new \Exception("Refund failed.");
    }
}