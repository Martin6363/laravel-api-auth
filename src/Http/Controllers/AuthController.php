<?php

namespace Martin6363\ApiAuth\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Martin6363\ApiAuth\Http\Requests\ForgotPasswordRequest;
use Martin6363\ApiAuth\Http\Requests\LoginRequest;
use Martin6363\ApiAuth\Http\Requests\RegisterRequest;
use Martin6363\ApiAuth\Http\Requests\ResetPasswordRequest;
use Martin6363\ApiAuth\Services\AuthService;
use Martin6363\ApiAuth\Services\EmailVerificationService;
use Martin6363\ApiAuth\Services\PasswordService;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     */
    public function __construct(
        protected AuthService $authService,
        protected PasswordService $passwordService,
        protected EmailVerificationService $verificationService
    ) {}

    /**
     * Register a new user.
     *
     * @param  RegisterRequest  $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());
        
        return response()->json([
            'message' => __('auth.registered'),
            'data' => $result,
        ], 201);
    }

    /**
     * Authenticate a user and return token.
     *
     * @param  LoginRequest  $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());
        
        return response()->json([
            'message' => __('auth.login_success'),
            'data' => $result,
        ]);
    }

    /**
     * Logout the authenticated user.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());
        
        return response()->json([
            'message' => __('auth.logout_success'),
        ]);
    }

    /**
     * Refresh the user's access token.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $result = $this->authService->refreshToken($request->user());
        
        return response()->json([
            'message' => __('auth.token_refreshed'),
            'data' => $result,
        ]);
    }

    /**
     * Get the authenticated user's profile.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function profile(Request $request): JsonResponse
    {
        $result = $this->authService->getProfile($request->user());
        
        return response()->json([
            'message' => __('auth.profile_retrieved'),
            'data' => $result,
        ]);
    }

    /**
     * Send password reset link.
     *
     * @param  ForgotPasswordRequest  $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $message = $this->passwordService->sendResetLink($request->validated());
        
        return response()->json([
            'message' => $message,
        ]);
    }

    /**
     * Reset user password.
     *
     * @param  ResetPasswordRequest  $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $message = $this->passwordService->reset($request->validated());
        
        return response()->json([
            'message' => $message,
        ]);
    }

    /**
     * Send email verification notification.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function sendVerification(Request $request): JsonResponse
    {
        $message = $this->verificationService->sendNotification($request->user());
        
        return response()->json([
            'message' => $message,
        ]);
    }

    /**
     * Verify user email.
     *
     * @param  Request  $request
     * @param  int|string  $id
     * @param  string  $hash
     * @return JsonResponse
     */
    public function verify(Request $request, $id, string $hash): JsonResponse
    {
        $message = $this->verificationService->verify($id, $hash);
        
        return response()->json([
            'message' => $message,
        ]);
    }
}