<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PurchaseController;

Route::post('/login', [AuthController::class, 'login']);

Route::post('/purchase', [PurchaseController::class, 'store']);