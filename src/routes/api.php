<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\GatewayController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/purchase', [PurchaseController::class, 'store']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/transactions', [TransactionController::class, 'index']);

    Route::get('/transactions/{id}', [TransactionController::class, 'show']);

    Route::post('/transactions/{id}/refund', [TransactionController::class, 'refund']);

});

Route::middleware(['auth:sanctum','role:admin'])->group(function () {

    Route::get('/gateways', [GatewayController::class, 'index']);

    Route::post('/gateways/{id}/activate', [GatewayController::class, 'activate']);

    Route::post('/gateways/{id}/deactivate', [GatewayController::class, 'deactivate']);

    Route::patch('/gateways/{id}/priority', [GatewayController::class, 'updatePriority']);

});