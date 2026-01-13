<?php

use Illuminate\Support\Facades\Route;
use Vendor\ApiAuth\Http\Controllers\Api\Auth\AuthController;

Route::prefix('api/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/email/verification-notification', [AuthController::class, 'sendVerification']);
        Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});