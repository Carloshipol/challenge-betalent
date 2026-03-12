<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\Payments\PaymentService;
use App\Enums\TransactionStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPayment implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Transaction $transaction,
        public array $payload
    ) {}

    public function handle(PaymentService $paymentService)
    {
        $this->transaction->update([
            'status' => TransactionStatus::PROCESSING
        ]);

        try {

            $paymentService->process($this->transaction, $this->payload);

            $this->transaction->update([
                'status' => TransactionStatus::SUCCESS
            ]);

        } catch (\Throwable $e) {

            $this->transaction->update([
                'status' => TransactionStatus::FAILED
            ]);

            throw $e;
        }
    }
}