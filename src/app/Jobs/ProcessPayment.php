<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\Payments\PaymentService;
use App\Enums\TransactionStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessPayment implements ShouldQueue
{
    use Queueable;

    public int $transactionId;
    public array $payload;

    public function __construct(int $transactionId, array $payload)
    {
        $this->transactionId = $transactionId;
        $this->payload = $payload;
    }

    public function handle(PaymentService $paymentService): void
    {
        $transaction = Transaction::findOrFail($this->transactionId);

        $transaction->update([
            'status' => TransactionStatus::PROCESSING
        ]);

        try {

            $paymentService->process($transaction, $this->payload);

            $transaction->update([
                'status' => TransactionStatus::SUCCESS
            ]);

        } catch (\Throwable $e) {

            Log::error('Payment processing failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);

            $transaction->update([
                'status' => TransactionStatus::FAILED
            ]);
        }
    }
}