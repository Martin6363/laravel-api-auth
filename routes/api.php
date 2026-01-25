<?php

use Illuminate\Support\Facades\Route;
use Martin6363\ApiAuth\Http\Controllers\v1\AuthController;

$prefix = config('api-auth.routes.prefix', 'api/auth');
$middleware = config('api-auth.routes.middleware', ['api']);
$enabled = config('api-auth.routes.enabled', []);

Route::prefix($prefix)->middleware($middleware)->group(function () use ($enabled) {
    $rateLimitEnabled = config('api-auth.rate_limiting.enabled', true);
    $throttleMiddleware = $rateLimitEnabled ? ['throttle.auth'] : [];

    // Public routes
    if ($enabled['register'] ?? true) {
        Route::post('/register', [AuthController::class, 'register'])
            ->middleware($throttleMiddleware);
    }

    if ($enabled['login'] ?? true) {
        Route::post('/login', [AuthController::class, 'login'])
            ->middleware($throttleMiddleware);
    }

    if ($enabled['forgot_password'] ?? true) {
        $passwordThrottle = $rateLimitEnabled
            ? ['throttle.auth:'.config('api-auth.rate_limiting.password_reset_max_attempts', 3)]
            : [];
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
            ->middleware($passwordThrottle);
    }

    if ($enabled['reset_password'] ?? true) {
        $passwordThrottle = $rateLimitEnabled
            ? ['throttle.auth:'.config('api-auth.rate_limiting.password_reset_max_attempts', 3)]
            : [];
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])
            ->middleware($passwordThrottle);
    }

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () use ($enabled) {
        if ($enabled['logout'] ?? true) {
            Route::post('/logout', [AuthController::class, 'logout']);
        }

        if ($enabled['refresh_token'] ?? true) {
            Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
        }

        if ($enabled['profile'] ?? true) {
            Route::get('/profile', [AuthController::class, 'profile']);
        }

        if ($enabled['email_verification'] ?? true) {
            Route::post('/email/verification-notification', [AuthController::class, 'sendVerification']);
            Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
                ->name('verification.verify');
        }
    });
});
