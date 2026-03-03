<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // User & Profile
    Route::get('/user/profile', [AuthController::class, 'me']);
    Route::post('/user/update', [AuthController::class, 'updateProfile']);
    Route::put('/user/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Wallet
    Route::get('/balance', [WalletController::class, 'getBalance']);
    Route::get('/users/search', [WalletController::class, 'searchUser']);
    Route::get('/wallet/stats', [WalletController::class, 'getStats']);

    // Transaction
    Route::get('/transactions', [TransactionController::class, 'getTransactions']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::post('/topup', [TransactionController::class, 'topup']);
    Route::post('/transfer', [TransactionController::class, 'transfer']);
});
