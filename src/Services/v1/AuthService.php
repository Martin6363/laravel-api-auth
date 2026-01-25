<?php

namespace Martin6363\ApiAuth\Services\v1;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Martin6363\ApiAuth\Http\Resources\v1\UserResource;

class AuthService
{
    /**
     * The user model class name.
     */
    protected string $userModel;

    public function __construct(
        protected EmailVerificationService $emailService,
    ) {
        $this->userModel = Config::get('api-auth.user_model');
    }

    /**
     * Register a new user.
     */
    public function register(array $data): array
    {
        $userData = $this->prepareUserData($data);

        $user = $this->userModel::create($userData);

        if (Config::get('api-auth.email_verification.send_on_register', false)) {
            $this->safeSendVerification($user);
        }

        Log::info('User registered successfully', ['user_id' => $user->id]);

        return $this->generateResponse($user);
    }

    /**
     * Authenticate a user and return token.
     *
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $password = $credentials['password'];
        $loginValue = $credentials['login'] ?? ($credentials['email'] ?? null);
        $columns = config('api-auth.login.search_columns', ['email']);

        $user = $this->userModel::where(function ($query) use ($columns, $loginValue) {
            foreach ($columns as $column) {
                $query->orWhere($column, $loginValue);
            }
        })->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            Log::warning('Failed login attempt', [
                'login_identifier' => $loginValue,
                'ip' => request()->ip(),
            ]);

            throw ValidationException::withMessages([
                'login' => [trans('api-auth::auth.failed')],
            ]);
        }

        Log::info('User logged in successfully', ['user_id' => $user->id]);

        return $this->generateResponse($user);
    }

    /**
     * Logout the authenticated user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     */
    public function logout($user): bool
    {
        $deleted = $user->currentAccessToken()->delete();

        if ($deleted) {
            Log::info('User logged out successfully', ['user_id' => $user->id]);
        }

        return $deleted;
    }

    /**
     * Refresh the user's access token.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     */
    public function refreshToken($user): array
    {
        // Delete current token
        $user->currentAccessToken()->delete();

        // Generate new token
        return $this->generateResponse($user);
    }

    /**
     * Get the authenticated user's profile.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     */
    public function getProfile($user): array
    {
        return [new UserResource($user)];
    }

    protected function safeSendVerification($user): void
    {
        try {
            $this->emailService->sendNotification($user);
        } catch (\Exception $e) {
            Log::warning('Verification email failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function prepareUserData(array $data): array
    {
        $fields = Config::get('api-auth.registration_fields', []);

        if (empty($fields)) {
            $fields = array_diff(
                array_keys(Config::get('api-auth.validation', [])),
                ['password', 'password_confirmation']
            );
        }

        $userData = array_intersect_key($data, array_flip($fields));
        foreach ($data as $key => $value) {
            if (! in_array($key, ['password', 'password_confirmation']) && ! isset($userData[$key])) {
                $userData[$key] = $value;
            }
        }

        if (isset($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        return $userData;
    }

    /**
     * Generate authentication response with user and token.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     */
    protected function generateResponse($user): array
    {
        $tokenConfig = config('api-auth.token', []);
        $tokenName = $tokenConfig['name'] ?? 'auth_token';
        $abilities = $tokenConfig['abilities'] ?? ['*'];
        $expiresAt = $tokenConfig['expires_at'] ?? null;

        // Create token with Sanctum
        $token = $user->createToken($tokenName, $abilities);

        // Set expiration if configured
        if ($expiresAt !== null) {
            $token->accessToken->expires_at = now()->addMinutes($expiresAt);
            $token->accessToken->save();
        }

        $response = [
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ];

        if (config('api-auth.response.include_user', true)) {
            $response['user'] = new UserResource($user);
        }

        return $response;
    }
}
