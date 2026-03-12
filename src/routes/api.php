<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\TransactionController;

Route::post('/login', [AuthController::class, 'login']);

Route::post('/purchase', [PurchaseController::class, 'store']);

Route::post('/transactions/{id}/refund', [TransactionController::class, 'refund']);