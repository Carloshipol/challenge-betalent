<?php

namespace App\Http\Controllers;

use App\Services\Payments\RefundService;

class TransactionController extends Controller
{
    public function __construct(
        private RefundService $refundService
    ) {}

    public function refund($id)
    {
        $transaction = $this->refundService->refund($id);

        return response()->json($transaction);
    }
}