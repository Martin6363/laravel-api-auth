<?php

namespace Vendor\ApiAuth\Http\Controllers\Api\Auth;

use Orchestra\Workbench\Http\Requests\Auth\LoginRequest;
use Vendor\ApiAuth\Services\Auth\AuthService;
use Vendor\ApiAuth\Http\Controllers\Controller;
use Vendor\ApiAuth\Services\Auth\PasswordService;
use Vendor\ApiAuth\Services\Auth\EmailVerificationService;
use Vendor\ApiAuth\Http\Requests\Auth\RegisterRequest;
use Vendor\ApiAuth\Http\Requests\Auth\ForgotPasswordRequest;
use Vendor\ApiAuth\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected PasswordService $passwordService,
        protected EmailVerificationService $verificationService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());
        return response()->json($result, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());
        return response()->json($result);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $message = $this->passwordService->sendResetLink($request->validated());
        return response()->json(['message' => $message]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $message = $this->passwordService->reset($request->validated());
        return response()->json(['message' => $message]);
    }

    public function sendVerification(Request $request): JsonResponse
    {
        $message = $this->verificationService->sendNotification($request->user());
        return response()->json(['message' => $message]);
    }

    public function verify(Request $request, $id, $hash): JsonResponse
    {
        $message = $this->verificationService->verify($id, $hash);
        return response()->json(['message' => $message]);
    }
}