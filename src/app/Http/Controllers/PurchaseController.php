<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Services\Payments\PaymentService;

class PurchaseController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function store(PurchaseRequest $request)
    {
        $transaction = $this->paymentService->process(
            $request->validated()
        );

        return response()->json($transaction);
    }
}