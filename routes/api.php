<?php

use App\Http\Controllers\RequestController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('wallets')->group(function () {
    Route::post('/', [WalletController::class, 'create']);
    Route::get('{walletId}/balance', [WalletController::class, 'balance']);
    Route::get('tag/{walletTag}', [WalletController::class, 'byTag']);
    Route::get('user/{userId}', [WalletController::class, 'byUser']);
    Route::patch('{walletId}/status', [WalletController::class, 'updateStatus']);
    Route::post('ledger', [WalletController::class, 'recordLedger']);
});


Route::prefix('requests')->group(function () {
    Route::post('/', [RequestController::class, 'create']);
    Route::post('/{requestId}/process', [RequestController::class, 'process']);
    Route::get('/{requestId}', [RequestController::class, 'show']);
    Route::get('/user/{userId}', [RequestController::class, 'listByUser']);
});
